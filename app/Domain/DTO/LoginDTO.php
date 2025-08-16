<?php

namespace App\DTO;

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