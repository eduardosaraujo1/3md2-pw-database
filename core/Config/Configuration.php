<?php

namespace Core\Config;

class Configuration
{
    private static array $cachedConfigs = [];
    private array $config;

    public function __construct(string $fileName)
    {
        if (isset(self::$cachedConfigs[$fileName])) {
            $this->config = self::$cachedConfigs[$fileName];
            return;
        }

        $filePath = PROJECT_ROOT . "/config/$fileName.php";

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Configuration file '{$fileName}' not found at path '{$filePath}'.");
        }

        self::$cachedConfigs[$fileName] = require $filePath;
        $this->config = self::$cachedConfigs[$fileName];
    }

    public function get(): array
    {
        return $this->config;
    }
}