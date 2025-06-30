<?php
require '../core/autoload.php';
use Core\Database\Connection;

Connection::create();

// Collect
$login = $_POST["login"];
$email = $_POST["email"];
$senha = $_POST["senha"];

// Check if login or email already exists
$existingUsers = Connection::fetch(
    "SELECT * FROM tb_usuario WHERE login = :login OR email = :email",
    [
        "login" => $login,
        "email" => $email
    ]
);

if (count($existingUsers) > 0) {
    foreach ($existingUsers as $user) {
        if ($user['login'] === $login) {
            echo json_encode(["error" => "Login já está em uso"]);
            exit;
        }
        if ($user['email'] === $email) {
            echo json_encode(["error" => "Email já está em uso"]);
            exit;
        }
    }
}

Connection::query(
    query: "
INSERT INTO tb_usuario (login, email, senha) VALUES
(:login, :email, :senha)",
    params: [
        "login" => $login,
        "email" => $email,
        "senha" => $senha
    ]
);

$users = Connection::fetch("SELECT * FROM tb_usuario");

foreach ($users as $key => $value) {
    foreach ($value as $key2 => $value2) {
        if (is_int($key2)) {
            unset($users[$key][$key2]);
        }
    }
}

var_dump($users);