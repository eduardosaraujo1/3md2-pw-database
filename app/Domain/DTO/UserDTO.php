<?php

namespace App\Domain\DTO;

class UserDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $login,
        public readonly string $email,
        public readonly string $senha,
        public readonly string $telefone,
        public readonly ?array $foto = null,
        public readonly ?int $id = null,
    ) {
    }

    public function toArray()
    {
        return (array) $this;
    }
}