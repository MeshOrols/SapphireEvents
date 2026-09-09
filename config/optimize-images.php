<?php
/**
 * One-time maintenance script: shrinks existing oversized images in place
 * (same filename/extension, so no DB reference ever needs to change) by
 * capping width at 1920px and re-encoding at a quality that keeps them
 * visually identical. Every original is backed up before being touched.
 *
 * Usage:
 *   php config/optimize-images.php --dry-run   (report only, no changes)
 *   php config/optimize-images.php             (apply changes)
 */

$root = dirname(__DIR__);
$targets = [
    $root . '/public/assets/images',
    $root . '/public/assets/uploads/media/image',
];

$dryRun = in_array('--dry-run', $argv, true);
$maxWidth = 1920;
$jpegQuality = 82;
$webpQuality = 82;
$skipUnderBytes = 350 * 1024; // don't bother reprocessing already-small files
$backupDir = $root . '/storage/image-backup-' . date('Y-m-d_His');

$stats = ['scanned' => 0, 'skipped_small' => 0, 'skipped_type' => 0, 'errors' => 0, 'optimized' => 0, 'bytes_before' => 0, 'bytes_after' => 0];
$errorLog = [];

function humanSize(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . 'MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . 'KB';
    return $bytes . 'B';
}

function iterFiles(string $dir): Generator {
    if (!is_dir($dir)) return;
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->isFile()) yield $file->getPathname();
    }
}

foreach ($targets as $dir) {
    foreach (iterFiles($dir) as $path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            continue;
        }
        $stats['scanned']++;
        $size = filesize($path);

        if ($size < $skipUnderBytes) {
            $stats['skipped_small']++;
            continue;
        }

        $info = @getimagesize($path);
        if (!$info) {
            $stats['skipped_type']++;
            $errorLog[] = "Could not read image dimensions: $path";
            continue;
        }
        [$width, $height, $type] = $info;

        $needsResize = $width > $maxWidth;
        // Even if not oversized in dimensions, a large file benefits from re-encoding at target quality.
        $newWidth = $needsResize ? $maxWidth : $width;
        $newHeight = $needsResize ? (int)round($height * ($maxWidth / $width)) : $height;

        $srcImage = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default => false,
        };

        if (!$srcImage) {
            $stats['skipped_type']++;
            $errorLog[] = "Unsupported/corrupt image type: $path";
            continue;
        }

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $tmpPath = $path . '.tmp_optimized';
        $ok = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($canvas, $tmpPath, $jpegQuality),
            IMAGETYPE_PNG => imagepng($canvas, $tmpPath, 6),
            IMAGETYPE_WEBP => imagewebp($canvas, $tmpPath, $webpQuality),
            default => false,
        };

        imagedestroy($srcImage);
        imagedestroy($canvas);

        if (!$ok || !is_file($tmpPath)) {
            $stats['errors']++;
            $errorLog[] = "Re-encode failed: $path";
            @unlink($tmpPath);
            continue;
        }

        $newSize = filesize($tmpPath);

        if ($newSize >= $size) {
            // Re-encode didn't help (already efficient) — discard and keep original.
            @unlink($tmpPath);
            $stats['skipped_small']++;
            continue;
        }

        $stats['optimized']++;
        $stats['bytes_before'] += $size;
        $stats['bytes_after'] += $newSize;

        echo sprintf(
            "%s  %s -> %s  (%dx%d -> %dx%d)  %s\n",
            $dryRun ? '[DRY RUN]' : '[OPTIMIZED]',
            humanSize($size),
            humanSize($newSize),
            $width, $height, $newWidth, $newHeight,
            str_replace($root . '/', '', $path)
        );

        if ($dryRun) {
            @unlink($tmpPath);
            continue;
        }

        $relative = str_replace($root . '/', '', $path);
        $backupPath = $backupDir . '/' . $relative;
        @mkdir(dirname($backupPath), 0755, true);
        copy($path, $backupPath);

        rename($tmpPath, $path);
    }
}

echo "\n--- Summary ---\n";
echo "Scanned:          {$stats['scanned']}\n";
echo "Optimized:        {$stats['optimized']}\n";
echo "Skipped (small):  {$stats['skipped_small']}\n";
echo "Skipped (type):   {$stats['skipped_type']}\n";
echo "Errors:           {$stats['errors']}\n";
echo "Total before:     " . humanSize($stats['bytes_before']) . "\n";
echo "Total after:      " . humanSize($stats['bytes_after']) . "\n";
if ($stats['bytes_before'] > 0) {
    $savedPct = round((1 - $stats['bytes_after'] / $stats['bytes_before']) * 100, 1);
    echo "Saved:            " . humanSize($stats['bytes_before'] - $stats['bytes_after']) . " ({$savedPct}%)\n";
}
if (!$dryRun && $stats['optimized'] > 0) {
    echo "Backups saved to: " . str_replace($root . '/', '', $backupDir) . "\n";
}
if (!empty($errorLog)) {
    echo "\n--- Errors/Skips detail (" . count($errorLog) . ") ---\n";
    foreach (array_slice($errorLog, 0, 20) as $line) {
        echo "  $line\n";
    }
    if (count($errorLog) > 20) {
        echo "  ... and " . (count($errorLog) - 20) . " more\n";
    }
}
