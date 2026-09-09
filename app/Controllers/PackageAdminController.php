<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Models\Package;
use App\Models\PackageCategory;

class PackageAdminController extends Controller
{
    public function index()
    {
        if (!$this->isAdmin()) {
            $this->redirect(route('/admin/login'));
        }

        $package = new Package();
        $packages = $package->getAllWithCategory();

        $this->view('admin.packages.index', [
            'packages' => $packages,
            'csrf_token' => CSRF::getToken(),
        ]);
    }

    public function create()
    {
        if (!$this->isAdmin()) {
            $this->redirect(route('/admin/login'));
        }

        $category = new PackageCategory();

        $this->view('admin.packages.create', [
            'categories' => $category->all(),
            'csrf_token' => CSRF::getToken(),
        ]);
    }

    public function edit()
    {
        if (!$this->isAdmin()) {
            $this->redirect(route('/admin/login'));
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $package = new Package();
        $category = new PackageCategory();
        $item = $package->find($id);

        if (!$item) {
            $this->redirect(route('/admin/packages'));
        }

        $this->view('admin.packages.edit', [
            'package' => $item,
            'categories' => $category->all(),
            'csrf_token' => CSRF::getToken(),
        ]);
    }

    public function store()
    {
        if (!$this->isAdmin()) {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        if (!isset($_POST['_csrf_token']) || !CSRF::validate($_POST['_csrf_token'])) {
            http_response_code(403);
            $this->json(['error' => 'CSRF token invalid']);
            return;
        }

        $payload = $this->buildPayload($_POST, $_FILES);
        if (isset($payload['error'])) {
            http_response_code(422);
            $this->json(['error' => $payload['error']]);
            return;
        }

        $package = new Package();
        if ($package->create($payload)) {
            $this->json(['success' => true, 'message' => 'Package created successfully']);
            return;
        }

        http_response_code(500);
        $this->json(['error' => 'Failed to create package']);
    }

    public function update()
    {
        if (!$this->isAdmin()) {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        if (!isset($_POST['_csrf_token']) || !CSRF::validate($_POST['_csrf_token'])) {
            http_response_code(403);
            $this->json(['error' => 'CSRF token invalid']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(422);
            $this->json(['error' => 'Invalid ID']);
            return;
        }

        $packageModel = new Package();
        $current = $packageModel->find($id);
        if (!$current) {
            http_response_code(404);
            $this->json(['error' => 'Package not found']);
            return;
        }

        $currentImage = $current['image'] ?? null;
        $payload = $this->buildPayload($_POST, $_FILES, $currentImage, true);
        if (isset($payload['error'])) {
            http_response_code(422);
            $this->json(['error' => $payload['error']]);
            return;
        }

        if ($packageModel->update($id, $payload)) {
            $this->cleanupLocalImageIfReplaced($currentImage, $payload['image'] ?? null);
            $this->json(['success' => true, 'message' => 'Package updated successfully']);
            return;
        }

        http_response_code(500);
        $this->json(['error' => 'Failed to update package']);
    }

    public function delete()
    {
        if (!$this->isAdmin()) {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(422);
            $this->json(['error' => 'Invalid ID']);
            return;
        }

        $package = new Package();
        if ($package->delete($id)) {
            $this->json(['success' => true, 'message' => 'Package deleted successfully']);
            return;
        }

        http_response_code(500);
        $this->json(['error' => 'Failed to delete package']);
    }

    private function buildPayload(array $post, array $files, ?string $currentImage = null, bool $allowRemoveImage = false): array
    {
        $title = $this->sanitize($post['title'] ?? '');
        $description = $this->sanitize($post['description'] ?? '');
        $priceLabel = $this->sanitize($post['price_label'] ?? '');
        $currency = $this->sanitize($post['currency'] ?? 'EUR');
        $priceAmount = (isset($post['price_amount']) && $post['price_amount'] !== '') ? (float)$post['price_amount'] : null;
        $categoryId = (int)($post['category_id'] ?? 0);
        $features = trim($post['features'] ?? '');
        $isFeatured = isset($post['is_featured']) ? 1 : 0;
        $displayOrder = (int)($post['display_order'] ?? 0);

        if ($title === '' || $description === '' || $priceLabel === '' || $categoryId <= 0) {
            return ['error' => 'Title, category, description and price label are required'];
        }

        $image = $currentImage;
        $imageUrl = trim((string)($post['image_url'] ?? ''));
        $removeImage = $allowRemoveImage && isset($post['remove_image']);
        $imageFile = $files['image'] ?? null;
        $hasUploadedFile = $imageFile && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === 0;
        $uploadErrorCode = $imageFile['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($imageFile && $uploadErrorCode !== UPLOAD_ERR_NO_FILE && $uploadErrorCode !== 0) {
            return ['error' => 'Image upload failed. Please try another file'];
        }

        if ($removeImage && ($imageUrl !== '' || $hasUploadedFile)) {
            return ['error' => 'Choose only one image action: remove, URL, or file upload'];
        }

        if ($imageUrl !== '' && $hasUploadedFile) {
            return ['error' => 'Provide either an image URL or an uploaded file, not both'];
        }

        if ($removeImage) {
            $image = null;
        } elseif ($imageUrl !== '') {
            if (!$this->isValidImageUrl($imageUrl)) {
                return ['error' => 'Invalid image URL. Use http://, https://, or a site path starting with /'];
            }
            $image = $imageUrl;
        } elseif ($hasUploadedFile) {
            // 1. Prepare the new WebP filename
            $extension = 'webp';
            $filename = 'pkg_' . uniqid() . '_' . time() . '.' . $extension;
            
            // 2. Define the save path (ensure PUBLIC_PATH is defined in your app)
            $targetDir = PUBLIC_PATH . '/assets/images/';
            $targetPath = $targetDir . $filename;

            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            // 3. Use our cruncher instead of the default ImageProcessor
            if ($this->crunchPackageImage($imageFile['tmp_name'], $targetPath)) {
                $image = 'assets/images/' . $filename;
            } else {
                // Fallback only if the cruncher fails
                $saved = \App\Core\ImageProcessor::process($imageFile);
                if (!$saved) {
                    return ['error' => 'Failed to upload and process image'];
                }
                $image = $saved;
            }
        }

        return $this->appendLocalizedFields([
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'features' => $features,
            'price_label' => $priceLabel,
            'currency' => $currency ?: 'EUR',
            'price_amount' => $priceAmount,
            'image' => $image,
            'is_featured' => $isFeatured,
            'display_order' => $displayOrder,
        ], $post, ['title', 'description', 'features', 'price_label']);
    }

    private function isValidImageUrl(string $url): bool
    {
        if ($url === '' || strlen($url) > 255) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return preg_match('#^/[^\s]+$#', $url) === 1;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true);
    }

    private function cleanupLocalImageIfReplaced(?string $oldImage, ?string $newImage): void
    {
        if (!$this->isLocalUploadReference($oldImage)) {
            return;
        }

        if ($oldImage === $newImage) {
            return;
        }

        \App\Core\ImageProcessor::delete($oldImage);
    }

    private function isLocalUploadReference(?string $image): bool
    {
        if (!$image) {
            return false;
        }

        if (preg_match('/^https?:\/\//', $image) || str_starts_with($image, '/')) {
            return false;
        }

        return true;
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['admin_id']);
    }
    
    private function crunchPackageImage(string $sourcePath, string $targetPath): bool 
{
    list($width, $height, $type) = getimagesize($sourcePath);
    
    // Max width for package detail images
    $maxWidth = 1200; 

    if ($width > $maxWidth) {
        $ratio = $maxWidth / $width;
        $newWidth = $maxWidth;
        $newHeight = (int)($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    switch ($type) {
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($sourcePath); break;
        case IMAGETYPE_PNG:  $image = imagecreatefrompng($sourcePath); break;
        case IMAGETYPE_WEBP: $image = imagecreatefromwebp($sourcePath); break;
        default: return false; 
    }

    if (!$image) return false;

    $tmpCanvas = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($tmpCanvas, false);
    imagesavealpha($tmpCanvas, true);
    imagecopyresampled($tmpCanvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Quality 75 will shrink MBs to KBs effectively
    $result = imagewebp($tmpCanvas, $targetPath, 75); 

    imagedestroy($image);
    imagedestroy($tmpCanvas);
    return $result;
}
}
