<?php
require 'core/autoload.php';
use Core\Database\Connection;

Connection::create([
    "host" => "localhost",
    "username" => "root",
    "password" => "root",
    "database" => "learning",
    "port" => "3306"
]);

$result = Connection::query("
INSERT INTO tb_usuario (login, email, senha) VALUES
(:login, :email, :senha)", ["login" => "eduardo2", "email" => "edu2@gmail.com", "senha" => "124235324"]);

var_dump($result);