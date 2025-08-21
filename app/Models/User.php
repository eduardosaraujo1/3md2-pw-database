<?php

namespace App\Models;

class User extends Model
{
    public function __construct(
        public string $nome,
        public string $login,
        public string $senha,
        public string $email,
        public string $telefone,
        public ?string $foto,
        public ?int $id,
    ) {
    }

    public static function fromArray(array $arr): static
    {
        return new User(
            id: (int) $arr['id'] ?? null,
            nome: $arr['nome'] ?? '',
            login: $arr['login'] ?? '',
            senha: $arr['senha'] ?? '',
            email: $arr['email'] ?? '',
            telefone: $arr['telefone'] ?? '',
            foto: $arr['foto'] ?? null
        );
    }

    public function setFoto(string $photoPath)
    {
        $this->foto = $photoPath;
    }
}