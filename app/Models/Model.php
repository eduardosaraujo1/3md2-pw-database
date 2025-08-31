<?php

namespace App\Models;

abstract class Model
{
    public function toJson()
    {
        return json_encode((array) $this);
    }

    public static function fromArray(array $data): static
    {
        throw new \Exception("Method fromArray must be implemented in the child class.");
    }

    public function toArray(): array
    {
        throw new \Exception("Method toArray must be implemented in the child class.");
    }
}