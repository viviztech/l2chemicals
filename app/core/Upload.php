<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Upload
{
    private const TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public static function image(array $file, string $folder = 'products'): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('The image upload failed.');
        if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) throw new RuntimeException('Images must be 5 MB or smaller.');
        if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) throw new RuntimeException('Invalid uploaded file.');

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::TYPES[$mime]) || getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('Only valid JPG, PNG and WEBP images are allowed.');
        }
        $allowedFolders = ['products', 'categories', 'general'];
        if (!in_array($folder, $allowedFolders, true)) throw new RuntimeException('Invalid upload destination.');

        $name = bin2hex(random_bytes(20)) . '.' . self::TYPES[$mime];
        $relative = 'uploads/' . $folder . '/' . $name;
        $destination = BASE_PATH . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('Unable to save the uploaded image.');
        return $relative;
    }
}

