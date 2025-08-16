<?php

namespace App\Support;

use Exception;

/**
 * @template T
 */
abstract class Result
{
    public static function error(Exception $error): Result
    {
        return new ResultError($error);
    }

    /**
     * @param T $value
     */
    public static function ok($value): Result
    {
        return new ResultOk($value);
    }
}

class ResultError extends Result
{
    public function __construct(public Exception $error)
    {
    }
}

/**
 * @template T
 */
class ResultOk extends Result
{
    /**
     * @param T $value
     */
    public function __construct(public mixed $value)
    {
    }
}