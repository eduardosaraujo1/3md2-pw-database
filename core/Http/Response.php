<?php

namespace Core\Http;

class Response
{
    public static function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public static function error(string $message, int $code = 400)
    {
        http_response_code($code);
        self::json(["error" => $message]);
        die();
    }

    public static function view(string $path, int $code = 200)
    {
        http_response_code($code);
        $path = realpath(PROJECT_ROOT . "/resources/views/$path.html");
        if ($path) {
            require $path;
        } else {
            echo '<h1>Erro: HTML da rota não encontrado</h1>';
        }
        die();
    }

    public static function redirect(string $location, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $location");
        die();
    }
}