<?php

namespace Php\Core\Services;

use Core\Database\Connection;
use Php\Core\Utils\Storage;

class ContatoService
{
    /**
     * @param array{nome:string,login:string,email:string,senha:string,telefone:string,foto:?object} $data
     */
    public function store(array $data)
    {
        // Pull all variables
        extract(array: $data);

        // Remove duplicates
        $existing = Connection::fetch(
            query: "SELECT * FROM tb_contato WHERE login = :login OR email = :email",
            params: [
                "login" => $login,
                "email" => $email
            ]
        );

        foreach ($existing as $contato) {
            if ($contato['login'] === $login) {
                throw new \InvalidArgumentException("Login já está em uso");
            }
            if ($contato['email'] === $email) {
                throw new \InvalidArgumentException("Email já está em uso");
            }
        }

        // Store photo
        $photo_path = $foto ? Storage::store($foto) : null;

        // Insert registry
        Connection::query(
            query: "
INSERT INTO tb_contato (nome, login, senha, email, telefone, foto) VALUES
(:nome, :login, :senha, :email, :telefone, :foto)",
            params: [
                "nome" => $nome,
                "login" => $login,
                "senha" => $senha,
                "email" => $email,
                "telefone" => $telefone,
                "foto" => $photo_path
            ]
        );

        // read last result
        $users = Connection::fetch("SELECT * FROM tb_contato ORDER BY id DESC LIMIT 1");

        /*
        // EQUIVALENTE DO CÓDIGO ABAIXO
        foreach ($users as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (!is_string($key2)) {
                    unset($users[$key][$key2]);
                }
            }
        }
        return $users
        */
        return array_map(
            callback: fn($user) => array_filter(
                array: $user,
                callback: 'is_string',
                mode: ARRAY_FILTER_USE_KEY
            ),
            array: $users
        );
    }
}