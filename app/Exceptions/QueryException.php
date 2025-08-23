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
        $sqlErrorCode = is_numeric($exception->getCode()) ? (int) $exception->getCode() : 0;
        return new self(
            message: $exception->getMessage(),
            sqlErrorCode: $sqlErrorCode,
            previous: $exception
        );
    }
}
