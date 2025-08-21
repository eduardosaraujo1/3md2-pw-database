<?php

namespace App\Exceptions;

class QueryException extends UserException
{
    public function __construct(string $message, int $sqlErrorCode, ?\Throwable $previous = null)
    {
        $fullMessage = "SQL Error Code {$sqlErrorCode}: {$message}";
        parent::__construct($fullMessage, $sqlErrorCode, $previous);
    }

    public static function fromPDOException(\PDOException $exception): self
    {
        return new self(
            message: $exception->getMessage(),
            sqlErrorCode: $exception->getCode(),
            previous: $exception
        );
    }
}
