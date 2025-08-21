<?php

namespace App\Repositories;

use App\Models\User;
use App\Exceptions\QueryException;
use Core\Services\Database;

class UserRepository
{
    public string $table = 'tb_contato';

    public function __construct(
        public Database $databaseService
    ) {
    }

    /**
     * Aviso: aplica hash bcrypt12 nas senhas
     * @param User $user
     * @return bool
     */
    public function insert(User $user): bool
    {
        try {
            return $this->databaseService->query(
                query: "
                    INSERT INTO $this->table (nome, login, senha, email, telefone, foto) VALUES
                    (:nome, :login, :senha, :email, :telefone, :foto)",
                params: [
                    "nome" => $user->nome,
                    "login" => $user->login,
                    "senha" => password_hash($user->senha, PASSWORD_BCRYPT),
                    "email" => $user->email,
                    "telefone" => $user->telefone,
                    "foto" => $user->foto
                ]
            );
        } catch (\PDOException $e) {
            throw QueryException::fromPDOException($e);
        }
    }

    /**
     * Atualiza os dados do usuário.
     * Aviso: aplica hash bcrypt12 na senha.
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $id = $user->id;
        try {
            return $this->databaseService->query(
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
                    "nome" => $user->nome,
                    "login" => $user->login,
                    "senha" => password_hash($user->senha, PASSWORD_BCRYPT),
                    "email" => $user->email,
                    "telefone" => $user->telefone,
                ]
            );
        } catch (\PDOException $e) {
            throw QueryException::fromPDOException($e);
        }
    }

    public function getLatest(): User|null
    {
        $users = $this->databaseService->fetch("SELECT * FROM $this->table ORDER BY id DESC LIMIT 1");
        $user = $users[0] ?? null;

        if (!$user)
            return null;

        return User::fromArray($user);
    }

    public function findById(string $id): ?User
    {
        try {
            $data = $this->databaseService->fetch(
                query: "SELECT * FROM {$this->table} WHERE id = :id",
                params: ['id' => $id]
            );

            if (empty($data)) {
                return null;
            }

            return User::fromArray($data[0] ?? []);
        } catch (\PDOException $e) {
            throw QueryException::fromPDOException($e);
        }
    }

    public function findByLoginAndPassword(string $login, string $password): ?User
    {
        try {
            $data = $this->databaseService->fetch(
                query: "SELECT * FROM {$this->table} WHERE login = :login OR email = :email",
                params: [
                    "login" => $login,
                    "email" => $login
                ]
            );

            if (empty($data)) {
                return null;
            }

            $user = User::fromArray($data[0] ?? []);

            if (!password_verify($password, $user->senha)) {
                return null;
            }

            return $user;
        } catch (\PDOException $e) {
            throw QueryException::fromPDOException($e);
        }
    }

    public function all(): array
    {
        try {
            $data = $this->databaseService->fetch("SELECT * FROM {$this->table}");

            return array_map(fn(array $user) => User::fromArray($user), $data);
        } catch (\PDOException $e) {
            throw QueryException::fromPDOException($e);
        }
    }
}