<?php

namespace Core\Http;

class Request
{
    public function __construct(
        private array $getParams,
        private array $postParams,
        private array $cookies,
        private array $files,
        private array $server
    ) {
    }

    public static function createFromGlobals()
    {
        return new Request(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    public function all(): array
    {
        return array_merge($this->getParams, $this->postParams, $this->files);
    }

    public function only(array $keys): array
    {
        $allParams = $this->all();
        return array_filter(
            $allParams,
            fn($key) => in_array($key, $keys, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }
}