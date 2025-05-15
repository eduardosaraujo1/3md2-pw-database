<?php
require '../core/autoload.php';
use Core\Database\Connection;

Connection::create([
    "host" => "localhost",
    "username" => "root",
    "password" => "root",
    "database" => "learning",
    "port" => "3306"
]);

// Collect
$login = $_POST["login"];
$email = $_POST["email"];
$senha = $_POST["senha"];

// Insert
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