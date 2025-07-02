<?php

namespace Php\Core\Utils;

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
}