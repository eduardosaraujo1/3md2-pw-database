<?php

namespace Core\Support;

use Core\Http\Response;

class ResponseFactory
{
    public static function fromArray(array $data, int $code = 200): Response
    {
        return response()->json(data: $data, code: $code);
    }

    public static function fromString(string $message, int $code = 200): Response
    {
        $body = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        return response(body: $body, status: $code);
    }
}
