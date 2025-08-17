<?php

namespace Core\Services;

use RuntimeException;

class Storage
{
    private string $storageDir;

    public function __construct(string $storageDir = '/storage')
    {
        $this->storageDir = PROJECT_ROOT . $storageDir;
        $this->ensureStorageDirectoryExists();
    }

    public function storeFile(string $tmpFilePath, string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $filename = $this->generateUuidFilename($extension);
        $targetPath = $this->getTargetPath($filename);

        if (!move_uploaded_file($tmpFilePath, $targetPath)) {
            throw new RuntimeException("Failed to move the uploaded file.");
        }

        return $this->getRelativePath($filename);
    }

    private function ensureStorageDirectoryExists(): void
    {
        if (!is_dir($this->storageDir)) {
            if (!mkdir($this->storageDir, 0755, true)) {
                throw new RuntimeException("Failed to create the storage directory.");
            }
        }
    }

    private function generateUuidFilename(string $extension): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)) . "." . $extension;
    }

    private function getTargetPath(string $filename): string
    {
        return "$this->storageDir/$filename";
    }

    private function getRelativePath(string $filename): string
    {
        return "/storage/$filename";
    }
}
