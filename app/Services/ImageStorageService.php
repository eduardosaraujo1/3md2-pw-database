<?php

namespace App\Services;

class ImageStorageService
{
    // TODO: Utilizar classe Core\Services\Storage
    private array $allowedTypes;
    private int $maxFileSize;

    /**
     * Instantiate a new ImageStorageService
     * @param string[] $allowedTypes // Allowed file extensions
     * @param int $maxFileSize Maximum file size in Kilobytes
     */
    public function __construct(array $allowedTypes = ["jpg", "jpeg", "png"], int $maxFileSize = 100 * 1024)
    {
        $this->allowedTypes = $allowedTypes;
        $this->maxFileSize = $maxFileSize * 1024; // kilobytes to bytes
    }

    public function store(?array $file)
    {
        $this->validateFile($file);

        $tmp = $file["tmp_name"];
        $originalName = $file["name"];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $filename = $this->generateFilename($extension);
        $targetPath = $this->getTargetPath($filename);

        $this->ensureStorageDirectoryExists();
        $this->moveUploadedFile($tmp, $targetPath);

        return $this->getRelativePath($filename);
    }

    private function validateFile(array $file): void
    {
        if ($file["error"] !== 0) {
            throw new \RuntimeException("Erro ao enviar o arquivo.");
        }

        if ($file["size"] > $this->maxFileSize) {
            throw new \RuntimeException("Arquivo excede o tamanho máximo permitido.");
        }

        $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowedTypes)) {
            throw new \RuntimeException("Tipo de arquivo não permitido.");
        }
    }

    private function generateFilename(string $extension): string
    {
        return $this->uuidv4() . '.' . $extension;
    }

    private function getTargetPath(string $filename): string
    {
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . '/storage';
        return "$storageDir/$filename";
    }

    private function getRelativePath(string $filename): string
    {
        return "/storage/$filename";
    }

    private function ensureStorageDirectoryExists(): void
    {
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . '/storage';

        if (!is_dir($storageDir)) {
            if (!mkdir($storageDir, 0755, true)) {
                throw new \RuntimeException("Falha ao criar o diretório de upload.");
            }
        }
    }

    private function moveUploadedFile(string $tmp, string $targetPath): void
    {
        if (!move_uploaded_file($tmp, $targetPath)) {
            throw new \RuntimeException("Falha ao mover o arquivo enviado.");
        }
    }

    private function uuidv4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}