<?php

namespace App\Domain\DTO;

class LoginDTO
{
    public function __construct(
        public string $login,
        public string $senha,
    ) {
    }

    public function toArray()
    {
        return (array) $this;
    }
}