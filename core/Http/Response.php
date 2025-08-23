<?php

namespace Core\Http;

class Response
{
    public function __construct(
        private string $body = '',
        private int $status = 200,
        private array $headers = [],
    ) {
    }

    public function header(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function send(): void
    {
        http_response_code($this->status ?? 500);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->body ?? "";
    }

    public function redirect(string $location, int $statusCode = 302): self
    {
        $this->status = $statusCode;
        $this->header('Location', $location);

        return $this;
    }

    public function json(array $data, int $code = 200): self
    {
        $this->header('Content-Type', 'application/json');
        $this->status = $code;
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    public function view(string $path, int $code = 200): self
    {
        $this->status = $code;
        $viewPath = realpath(PROJECT_ROOT . "/resources/views/$path.html");

        if ($viewPath && file_exists($viewPath)) {
            ob_start();
            require $viewPath;
            $this->body = ob_get_clean() ?: '<h1>Error: View not found</h1>';
        } else {
            $this->body = '<h1>Error: View not found</h1>';
        }

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}