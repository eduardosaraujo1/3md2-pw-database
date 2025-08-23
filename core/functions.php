<?php

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die();
    }
}

if (!function_exists('dump')) {
    function dump(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
    }
}

if (!function_exists('config')) {
    function config(string $file): array
    {
        // The configuration is cached statically for performance.
        static $config = [];

        if (!defined('PROJECT_ROOT')) {
            throw new \RuntimeException('PROJECT_ROOT is not defined.');
        }

        // Load the configuration file if not already loaded
        if (!isset($config[$file])) {
            $filePath = PROJECT_ROOT . "/config/{$file}.php";
            if (!file_exists($filePath)) {
                throw new \RuntimeException("Configuration file '{$file}.php' not found in config directory.");
            }

            $loadedConfig = require $filePath;

            if (!is_array($loadedConfig)) {
                throw new \RuntimeException("Configuration file '{$file}.php' must return an array.");
            }

            $config[$file] = $loadedConfig;
        }

        return $config[$file];
    }
}

if (!function_exists('app')) {
    /**
     * Get the service container instance.
     *
     * /// O nome 'app' existe para manter consistência com o Laravel. Na prática, a função é `service_locator()` ou `sl()`
     */
    function app(): \Core\Container\Container
    {
        return \Core\Container\Container::getInstance();
    }
}

if (!function_exists('response')) {
    function response(string $body = '', int $status = 200, array $headers = []): \Core\Http\Response
    {
        return new \Core\Http\Response($body, $status, $headers);
    }
}