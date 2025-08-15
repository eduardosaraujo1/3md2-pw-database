<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\DatabaseService;

class UserRepository
{
    public string $table = 'tb_contato';

    public function __construct()
    {
    }

    /**
     * Aviso: aplica hash bcrypt12 nas senhas
     *  @param array{nome:string,login:string,senha:string,email:string,telefone:string,foto:string} $data
     *  @return bool
     */
    public function insert(array $data): bool
    {
        return DatabaseService::query(
            query: "
                INSERT INTO $this->table (nome, login, senha, email, telefone, foto) VALUES
                (:nome, :login, :senha, :email, :telefone, :foto)",
            params: [
                "nome" => $data['nome'],
                "login" => $data['login'],
                "senha" => password_hash($data['senha'], PASSWORD_BCRYPT),
                "email" => $data['email'],
                "telefone" => $data['telefone'],
                "foto" => $data['foto']
            ]
        );
    }

    /**
     * Atualiza os dados do usuário.
     * Aviso: aplica hash bcrypt12 na senha.
     * @param int $id Identificador do usuário a ser atualizado.
     * @param array{nome:string,login:string,senha:string,email:string,telefone:string,foto:string} $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return DatabaseService::query(
            query: "
            UPDATE {$this->table} SET
                nome = :nome,
                login = :login,
                senha = :senha,
                email = :email,
                telefone = :telefone
            WHERE id = :id
        ",
            params: [
                "id" => $id,
                "nome" => $data['nome'],
                "login" => $data['login'],
                "senha" => password_hash($data['senha'], PASSWORD_BCRYPT),
                "email" => $data['email'],
                "telefone" => $data['telefone'],
            ]
        );
    }

    public function getLatest(): User|null
    {
        $users = DatabaseService::fetch("SELECT * FROM $this->table ORDER BY id DESC LIMIT 1");
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

    public function findById(string $id): ?User
    {
        $data = DatabaseService::fetch(
            query: "SELECT * FROM {$this->table} WHERE id = :id",
            params: ['id' => $id]
        );

        if (empty($data)) {
            return null;
        }

        return User::fromArray($data[0]);
    }

    public function findByLoginAndPassword(string $login, string $password): ?User
    {
        $data = DatabaseService::fetch(
            query: "SELECT * FROM {$this->table} WHERE login = :login OR email = :email",
            params: [
                "login" => $login,
                "email" => $login
            ]
        );

        if (empty($data)) {
            return null;
        }

        $user = User::fromArray($data[0]);

        if (!password_verify($password, $user->senha)) {
            return null;
        }

        return $user;
    }

    public function all(): array
    {
        $data = DatabaseService::fetch("SELECT * FROM {$this->table}");

        return array_map(fn($user) => User::fromArray($user), $data);
    }
}