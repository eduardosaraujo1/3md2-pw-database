<?php

namespace App\Domain\DTO;

class UserRegisterDTO
{
    public function __construct(
        public string $nome,
        public string $login,
        public string $email,
        public string $senha,
        public string $telefone,
        public ?array $foto = null,
    ) {
    }

    public function toArray()
    {
        return (array) $this;
    }
}