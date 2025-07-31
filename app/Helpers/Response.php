<?php

namespace App\Helpers;

class Response
{
    public static function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $code = 400)
    {
        http_response_code($code);
        self::json(["error" => $message]);
    }

    public static function view(string $path, int $code = 200)
    {
        http_response_code($code);
        $path = realpath(__DIR__ . "/../../resources/views/$path.html");
        if ($path) {
            require $path;
        } else {
            echo '<h1>Erro: HTML da rota não encontrado</h1>';
            die();
        }
    }
}