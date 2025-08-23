<?php

namespace Core\Http;

use App\Exceptions\UserException;
use Core\Routing\Router;
use Core\Support\ResponseFactory;

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

        try {
            $processed = $callback($request);
        } catch (UserException $e) {
            $message = $e->getMessage();
            $code = ($e->getCode() >= 400 && $e->getCode() < 500) ? $e->getCode() : 400;

            $processed = ResponseFactory::fromArray(
                data: ['message' => $message],
                code: $code
            );
        } catch (\Exception $e) {
            error_log("Route callback failed: " . $e->getMessage());

            $message = '500 Internal Server Error';
            $code = ($e->getCode() >= 500 && $e->getCode() < 600) ? $e->getCode() : 500;

            $processed = ResponseFactory::fromArray(
                data: ['message' => $message],
                code: $code
            );
        }

        if ($processed instanceof Response) {
            return $processed;
        } elseif (is_array($processed)) {
            return ResponseFactory::fromArray($processed);
        } elseif (is_string($processed)) {
            return ResponseFactory::fromString(message: $processed);
        }

        return ResponseFactory::fromArray(
            data: [
                'message' => '500 Internal Server Error'
            ],
            code: 500
        );
    }
}