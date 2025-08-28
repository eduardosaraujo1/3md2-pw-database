<?php

namespace Core\Routing;

use Core\Http\Request;
use Core\Http\Response;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put(string $path, callable $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable $handler): void
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function handle(string $method, string $uri): callable
    {
        // Remover query string da URL para ver se a rota está correta
        $uri_path = strtok($uri, '?');

        if (isset($this->routes[$method][$uri_path])) {
            return $this->routes[$method][$uri_path];
        }

        return function () {
            return response()->json(
                ['status' => 'error', 'message' => '404 Not Found'],
                404
            );
        };
    }
}