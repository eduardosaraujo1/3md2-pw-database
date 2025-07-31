<?php

namespace App\Models;

class User
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $login,
        public string $senha,
        public string $email,
        public string $telefone,
        public ?string $foto,
    ) {
    }

    public function toJson()
    {
        return json_encode((array) $this);
    }

    public static function fromArray(array $arr)
    {
        return new User(
            id: (int) $arr['id'],
            nome: $arr['nome'],
            login: $arr['login'],
            senha: $arr['senha'],
            email: $arr['email'],
            telefone: $arr['telefone'],
            foto: $arr['foto']
        );
    }
}