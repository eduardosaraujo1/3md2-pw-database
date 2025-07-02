<?php
require '../core/autoload.php';
use Php\Core\Services\ContatoService;
use Php\Core\Utils\Response;

// Collect data
try {
    $nome = $_POST["nome"];
    $login = $_POST["login"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $telefone = $_POST["telefone"];
    $foto = $_FILES["foto"] ?? null;

    $data = compact([
        'nome',
        'login',
        'email',
        'senha',
        'telefone',
        'foto'
    ]);

    $service = new ContatoService();
    $user = $service->store($data);

    Response::json($user);
} catch (Exception $e) {
    Response::error($e->getMessage());
}