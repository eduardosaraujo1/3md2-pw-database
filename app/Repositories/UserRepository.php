<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\DatabaseService;

class UserRepository implements Repository
{
    public string $table = 'tb_contato';

    public function __construct()
    {
    }

    /**
     *  @param array{nome:string,login:string,senha:string,email:string,telefone:string,foto:string} $data
     *  @return bool
     */
    public function insert(array $data): bool
    {
        return DatabaseService::query(
            query: "
                INSERT INTO tb_contato (nome, login, senha, email, telefone, foto) VALUES
                (:nome, :login, :senha, :email, :telefone, :foto)",
            params: [
                "nome" => $data['nome'],
                "login" => $data['login'],
                "senha" => $data['senha'],
                "email" => $data['email'],
                "telefone" => $data['telefone'],
                "foto" => $data['foto']
            ]
        );
    }

    public function getLatest(): User|null
    {
        $users = DatabaseService::fetch("SELECT * FROM tb_contato ORDER BY id DESC LIMIT 1");
        $user = $users[0] ?? null;

        if (!$user)
            return null;

        return User::fromArray($user);
    }

    /**
     * @return array{email: bool, login: bool}
     */
    public function checkDuplicates(string $login, string $email): array
    {
        $data = DatabaseService::fetch(
            query: "SELECT login, email FROM $this->table WHERE login = :login OR email = :email",
            params: [
                "login" => $login,
                "email" => $email
            ]
        );

        $result = [
            'email' => false,
            'login' => false
        ];

        foreach ($data as $row) {
            if (isset($row['email']) && $row['email'] === $email) {
                $result['email'] = true;
            }
            if (isset($row['login']) && $row['login'] === $login) {
                $result['login'] = true;
            }
        }

        return $result;
    }

}