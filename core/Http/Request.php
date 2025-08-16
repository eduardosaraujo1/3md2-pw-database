<?php

namespace Core\Http;

class Request
{
    private function __construct(
        private array $server,
        private array $get,
        private array $post,
        private array $cookie,
        private array $files
    ) {
    }

    public static function createFromGlobals()
    {
        return new Request(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }
}