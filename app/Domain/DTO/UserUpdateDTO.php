<?php

namespace App\Domain\DTO;

class UserUpdateDTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $nome,
        public readonly ?string $login,
        public readonly ?string $email,
        public readonly ?string $senha,
        public readonly ?string $telefone,
    ) {
    }

    public function toArray()
    {
        return (array) $this;
    }
}