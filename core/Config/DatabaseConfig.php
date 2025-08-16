<?php

namespace Core\Config;

class DatabaseConfig
{
    public string $host;
    public string $username;
    public string $password;
    public string $database;
    public int $port;

    private array $config;

    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("Configuration file not found at: {$filepath}");
        }

        $this->config = require $filepath;

        if (!is_array($this->config)) {
            throw new \UnexpectedValueException("Configuration file must return an array.");
        }

        $this->host = $this->config['host'] ?? 'localhost';
        $this->username = $this->config['username'] ?? 'root';
        $this->password = $this->config['password'] ?? '';
        $this->database = $this->config['database'] ?? '';
        $this->port = (int) ($this->config['port'] ?? 3306);
    }

    public function getRawConfig(): array
    {
        return $this->config;
    }
}