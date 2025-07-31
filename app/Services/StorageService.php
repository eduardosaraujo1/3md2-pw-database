<?php

namespace App\Services;

class StorageService
{
    public static function store(?array $file)
    {
        if ($file["error"] !== 0) {
            throw new \RuntimeException("Erro ao enviar o arquivo.");
        }

        $maxSize = 100 * 1024 * 1024; // 100 MB in bytes
        if ($file["size"] > $maxSize) {
            throw new \RuntimeException("Arquivo excede o tamanho máximo de 100 MB.");
        }

        $tmp = $file["tmp_name"];
        $originalName = $file["name"];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png"];

        if (!in_array($extension, $allowed)) {
            throw new \RuntimeException("Tipo de arquivo não permitido.");
        }

        // filename must be random UUID
        $filename = self::uuidv4() . '.' . $extension;
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . '/storage';
        $targetPath = "$storageDir/$filename";
        $relativePath = "/storage/$filename";

        if (!is_dir($storageDir)) {
            if (!mkdir($storageDir, 0755, true)) {
                throw new \RuntimeException("Falha ao criar o diretório de upload.");
            }
        }

        if (!move_uploaded_file($tmp, $targetPath)) {
            throw new \RuntimeException("Falha ao mover o arquivo enviado.");
        }

        return $relativePath;
    }

    public static function uuidv4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}