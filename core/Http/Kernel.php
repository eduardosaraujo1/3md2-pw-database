<?php

namespace Core\Http;

use Core\Routing\Router;

class Kernel
{
    public function __construct(private Router $router)
    {
    }
    public function handle(Request $request): Response
    {
        $uri = $request->uri();
        $method = $request->method();

        $callback = $this->router->handle(
            method: $method,
            uri: $uri
        );
        $processed = $callback($request);

        if ($processed instanceof Response) {
            error_log("Route callback failed to return a Response object.");
            return $processed;
        }

        return response()->json(
            data: ['status' => 'error', 'message' => '500 Internal Server Error'],
            code: 404
        );
    }
}