<?php

namespace App\Services;

use App\Exceptions\StorageException;
use Core\Services\Storage;
use RuntimeException;

class ImageStorageService
{
    private array $allowedTypes;
    private int $maxFileSize;
    private Storage $storage;

    /**
     * Instantiate a new ImageStorageService
     * @param Storage $storage Dependency for file storage
     * @param string[] $allowedTypes Allowed file extensions
     * @param int $maxFileSize Maximum file size in Kilobytes
     */
    public function __construct(Storage $storage, array $allowedTypes = ["jpg", "jpeg", "png"], int $maxFileSize = 100 * 1024)
    {
        $this->storage = $storage;
        $this->allowedTypes = $allowedTypes;
        $this->maxFileSize = $maxFileSize * 1024; // kilobytes to bytes
    }

    public function store(?array $file)
    {
        $this->validateFile($file);

        $tmp = $file["tmp_name"];
        $originalName = $file["name"];

        return $this->storage->storeFile($tmp, $originalName);
    }

    public function get(string $image_path): string
    {
        // Pegar dados da imagem a partir de StorageService
        return $this->storage->getFile($image_path);
    }

    private function validateFile(array $file): void
    {
        if ($file["error"] !== 0) {
            throw new RuntimeException("Erro ao enviar o arquivo.");
        }

        if ($file["size"] > $this->maxFileSize) {
            throw new RuntimeException("Arquivo excede o tamanho máximo permitido.");
        }

        $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowedTypes)) {
            throw new RuntimeException("Tipo de arquivo não permitido.");
        }
    }
}