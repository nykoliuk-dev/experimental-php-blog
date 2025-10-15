<?php
declare(strict_types=1);

namespace App\Service;

final class FileService
{
    public function __construct(private string $uploadDir)
    {
    }

    public function upload(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Ошибка загрузки файла.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

        if (!in_array($ext, $allowed, true)) {
            throw new \RuntimeException('Недопустимый тип файла.');
        }

        $uniqueName = uniqid('img_', true) . '.' . $ext;
        $targetPath = rtrim($this->uploadDir, '/') . '/' . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Failed to save file.');
        }

        return $uniqueName;
    }
}