<?php

declare(strict_types=1);

namespace App;

/**
 * Upload — file upload handling for business images.
 */
class Upload
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    public function __construct(private string $uploadDir)
    {
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }
    }

    public function save(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'No file uploaded.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload error code: ' . (int) $file['error']];
        }

        if ($file['size'] > self::MAX_SIZE) {
            return ['ok' => false, 'error' => 'File too large (max 5 MB).'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            return ['ok' => false, 'error' => 'Invalid file type. Only JPEG, PNG, WebP, GIF allowed.'];
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $this->uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['ok' => false, 'error' => 'Failed to save uploaded file.'];
        }

        return ['ok' => true, 'path' => $destination, 'url' => '/uploads/' . $filename];
    }
}