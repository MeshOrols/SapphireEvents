<?php

namespace App\Core;

/**
 * Lightweight bot protection for public forms: a hidden honeypot field,
 * a minimum time between page load and submit, and a per-IP submission limit.
 *
 * Callers should respond to a flagged submission exactly as they would to a
 * successful one (without saving it), so bots get no signal to adapt to.
 */
class SpamGuard
{
    private const HONEYPOT_FIELD = 'website';
    private const MIN_FILL_SECONDS = 3;
    private const MAX_SUBMISSIONS = 5;
    private const WINDOW_SECONDS = 3600;

    /**
     * Hidden form fields to echo inside a protected <form>. Also records when
     * the form was rendered for the minimum-fill-time check.
     */
    public static function fields(string $form): string
    {
        $_SESSION['_form_rendered_at'][$form] = time();

        return '<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">'
            . '<label>Leave this field empty'
            . '<input type="text" name="' . self::HONEYPOT_FIELD . '" value="" tabindex="-1" autocomplete="off">'
            . '</label></div>';
    }

    /**
     * Returns the reason a submission looks automated, or null if it looks human.
     */
    public static function check(string $form): ?string
    {
        if (trim((string)($_POST[self::HONEYPOT_FIELD] ?? '')) !== '') {
            return 'honeypot';
        }

        $renderedAt = $_SESSION['_form_rendered_at'][$form] ?? null;
        if ($renderedAt !== null && time() - (int)$renderedAt < self::MIN_FILL_SECONDS) {
            return 'too-fast';
        }

        if (count(self::updateHits(false)) >= self::MAX_SUBMISSIONS) {
            return 'rate-limit';
        }

        return null;
    }

    /**
     * Count a saved submission towards the sender's rate limit. Called only
     * after a successful save, so fixing validation errors never counts.
     */
    public static function recordSubmission(): void
    {
        self::updateHits(true);
    }

    public static function log(string $form, string $reason): void
    {
        error_log(sprintf('SpamGuard blocked %s submission (%s) from %s', $form, $reason, self::clientIp()));
    }

    /**
     * Returns the sender's submission timestamps within the window, pruning
     * older ones and optionally appending the current time.
     *
     * @return int[]
     */
    private static function updateHits(bool $addNow): array
    {
        $dir = STORAGE_PATH . '/rate-limits';
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            // Never block real customers because storage is unavailable.
            return [];
        }

        $handle = @fopen($dir . '/' . sha1(self::clientIp()) . '.json', 'c+');
        if ($handle === false) {
            return [];
        }

        try {
            flock($handle, LOCK_EX);
            $now = time();
            $hits = json_decode((string)stream_get_contents($handle), true);
            $hits = array_values(array_filter(
                is_array($hits) ? $hits : [],
                static fn ($t) => is_int($t) && $now - $t < self::WINDOW_SECONDS
            ));

            if ($addNow) {
                $hits[] = $now;
                ftruncate($handle, 0);
                rewind($handle);
                fwrite($handle, json_encode($hits));
            }

            return $hits;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private static function clientIp(): string
    {
        return (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }
}
