<?php

namespace Core\Http;

class Kernel
{
    public function __construct(private array $router)
    {
    }
    public function handle(Request $request)
    {
        $uri = $request->uri();

        if (!array_key_exists($uri, $this->router)) {
            return $this->notFound();
        }

        $callback = $this->router[$uri];
        $processed = $callback($request);

        if ($processed instanceof Response) {
            return $processed;
        }

        throw new \RuntimeException('Unexpected response type');
    }

    private function notFound()
    {
        return response(
            body: "<h1>404 Not Found</h1>",
            status: 404
        );
    }
}