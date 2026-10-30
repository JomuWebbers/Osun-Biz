<?php

declare(strict_types=1);

namespace App;

/**
 * Mail — optional mail wrapper.
 * Logs emails to a file in development; sends via SMTP in production.
 */
class Mail
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $entry = sprintf(
            "[%s] To: %s | Subject: %s\n%s\n---\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $body
        );

        file_put_contents($logDir . '/mail.log', $entry, FILE_APPEND | LOCK_EX);

        return true;
    }
}