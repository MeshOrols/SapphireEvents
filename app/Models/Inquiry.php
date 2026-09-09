<?php

namespace App\Models;

use App\Core\Model;

class Inquiry extends Model
{
    protected string $table = 'inquiries';
    private static bool $schemaEnsured = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

   public function createInquiry(array $data): bool
    {
        $payload = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'event_type' => $data['event_type'] ?? '',
            'message' => $data['message'] ?? '',
        ];

        if (!empty($data['event_date'])) {
            $payload['event_date'] = $data['event_date'];
        }

        // 1. Save to the database as usual
        $success = $this->create($payload);

        // 2. If it saved successfully, send the email immediately
        if ($success) {
            $this->sendNotificationEmail($payload);
            $this->sendClientAutoReply($payload); // THE NEW AUTO-RESPONDER
        }

        return $success;
    }

    /**
     * Send email notification to Admin
     */
    private function sendNotificationEmail(array $payload): void
    {
    $to = "info@sapphireeventglitz.com";
    $subject = "New Inquiry Submitted: " . ($payload['event_type'] ?: 'General');

    // --- DATE EXTRACTION LOGIC ---
    // Scan the message for a date (YYYY-MM-DD, DD-MM-YYYY, or MM/DD/YYYY)
    preg_match('/(\d{4}-\d{2}-\d{2}|\d{2}-\d{2}-\d{4}|\d{2}\/\d{2}\/\d{4})/', $payload['message'], $matches);
    
    // Priority: 1. Date found in message, 2. event_date field, 3. N/A
    $dateRaw = !empty($matches[0]) ? $matches[0] : ($payload['event_date'] ?? '');
    $displayDate = !empty($dateRaw) ? date('M d, Y', strtotime($dateRaw)) : 'N/A';
    // -----------------------------

    $body = "You have received a new inquiry from the website contact page.\n\n";
    $body .= "DETAILS:\n \n \n";
    $body .= "Name: " . $payload['name'] . "\n";
    $body .= "Email: " . $payload['email'] . "\n";
    $body .= "Phone: " . ($payload['phone'] ?: 'N/A') . "\n";
    $body .= "Event Type: " . $payload['event_type'] . "\n";
    
    // Use our new $displayDate variable here
    $body .= "Event Date: " . $displayDate . "\n\n"; 
    
    $body .= "MESSAGE:\n" . $payload['message'] . "\n\n";
    $body .= "View in Admin Panel: " . "https://" . $_SERVER['HTTP_HOST'] . "/admin/inquiries";

    $headers = [
        "From: Sapphire Admin <noreply@" . $_SERVER['HTTP_HOST'] . ">",
        "X-Mailer: PHP/" . phpversion()
    ];

    @mail($to, $subject, $body, implode("\r\n", $headers));
}

    private function sendClientAutoReply(array $payload): void
    {
    $to = $payload['email']; 
    $clientName = $payload['name'];
    $eventType = $payload['event_type'] ?: 'special event';

    // --- NEW DATE EXTRACTION LOGIC ---
    // Look for dates like 2026-12-25, 25-12-2026, or 12/25/2026 inside the message
    preg_match('/(\d{4}-\d{2}-\d{2}|\d{2}-\d{2}-\d{4}|\d{2}\/\d{2}\/\d{4})/', $payload['message'], $matches);
    
    // Determine which date to use: 
    // 1. Found in message? Use it. 
    // 2. Otherwise, use event_date field. 
    // 3. Otherwise, 'TBD'.
    $dateRaw = !empty($matches[0]) ? $matches[0] : ($payload['event_date'] ?? '');
    $eventDate = !empty($dateRaw) ? date('M d, Y', strtotime($dateRaw)) : 'TBD';
    // ---------------------------------

    $subject = "Thank you for your inquiry ✨💎 - Sapphire Events";

    // Professional HTML Email Body
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
            <h2 style='color: #0F3D3E;'>Hello $clientName,</h2><br>
            <p>Thank you for reaching out! We have successfully received your inquiry for your <strong>$eventType</strong>.</p>
            <p><strong>Proposed Date:</strong> $eventDate</p>
            <p>We are currently checking our availability and will get back to you with more details within 24-48 hours.</p>
            <p>In the meantime, feel free to browse our latest work on our website or follow us on Instagram for more inspiration</p>
            <hr style='border: 0; border-top: 1px solid #eee;' />
            <p style='font-size: 0.9em; color: #777;'>
                Best Regards,<br>
                <strong>Sapphire Events Team</strong><br>
                <a href='https://sapphireeventglitz.com' style='color: #0F3D3E;'>https://sapphireeventglitz.com</a>
            </p>
        </div>
    </body>
    </html>
    ";

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: Sapphire Event Glitz <noreply@sapphireeventglitz.com>'; 
    $headers[] = 'Reply-To: info@sapphireeventglitz.com'; 
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Send the email using the headers
    @mail($to, $subject, $message, implode("\r\n", $headers));
}

    public function getLatest(int $limit = 10)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSenderEmails(): array
    {
        $stmt = $this->connection->prepare(
            "SELECT DISTINCT email
             FROM {$this->table}
             WHERE email IS NOT NULL AND email <> ''
             ORDER BY email ASC"
        );
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $emails = [];
        foreach ($rows as $row) {
            $email = trim((string)($row['email'] ?? ''));
            if ($email !== '') {
                $emails[] = $email;
            }
        }

        return $emails;
    }

    private function ensureSchema(): void
    {
        if (self::$schemaEnsured) {
            return;
        }

        try {
            $this->connection->exec(
                "CREATE TABLE IF NOT EXISTS `inquiries` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(150) NOT NULL,
                    `email` VARCHAR(150) NOT NULL,
                    `phone` VARCHAR(50),
                    `event_type` VARCHAR(150),
                    `event_date` DATE NULL,
                    `message` TEXT NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_email` (`email`),
                    INDEX `idx_created` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $columnCheck = $this->connection->query("SHOW COLUMNS FROM `{$this->table}` LIKE 'event_date'");
            if (!$columnCheck->fetch()) {
                $this->connection->exec(
                    "ALTER TABLE `{$this->table}` ADD COLUMN `event_date` DATE NULL AFTER `event_type`"
                );
            }
        } catch (\Throwable $e) {
            // Keep runtime stable if DB user cannot alter schema.
        }

        self::$schemaEnsured = true;
    }
}
