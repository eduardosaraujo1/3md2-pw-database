<?php

namespace App\DTO;

class SignInCredentialsDTO
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