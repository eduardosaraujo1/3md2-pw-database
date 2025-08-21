<?php

namespace Core\Http;

use App\Exceptions\UserException;
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
        // Callback with default response handler
        try {
            $processed = $callback($request);
        } catch (UserException $e) {
            $processed = response()->json(
                data: ['status' => 'error', 'message' => $e->getMessage()],
                code: 400
            );
        } catch (\Exception $e) {
            error_log("Route callback failed: " . $e->getMessage());
            $processed = response()->json(['status' => 'error', 'message' => '500 Internal Server Error'], 500);
        }

        if ($processed instanceof Response) {
            return $processed;
        }

        return response()->json(
            data: ['status' => 'error', 'message' => '500 Internal Server Error'],
            code: 404
        );
    }
}