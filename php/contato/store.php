<?php
require '../core/autoload.php';
use Core\Database\Connection;

function uuidv4(): string
{
    $data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

Connection::create();

// Collect
$nome = $_POST["nome"];
$login = $_POST["login"];
$email = $_POST["email"];
$senha = $_POST["senha"];
$telefone = $_POST["telefone"];

$existingContatos = Connection::fetch(
    "SELECT * FROM tb_contato WHERE login = :login OR email = :email",
    [
        "login" => $login,
        "email" => $email
    ]
);

if (count($existingContatos) > 0) {
    foreach ($existingContatos as $contato) {
        if ($contato['login'] === $login) {
            echo json_encode(["error" => "Login já está em uso"]);
            exit;
        }
        if ($contato['email'] === $email) {
            echo json_encode(["error" => "Email já está em uso"]);
            exit;
        }
    }
}

// store uploaded photo
$photo_path = null;
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {
    // collect data
    $foto = $_FILES["foto"];
    $tmp = $foto["tmp_name"];
    $originalName = $foto["name"];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowed = ["jpg", "jpeg", "png"];
    if (!in_array($extension, $allowed)) {
        die("Tipo de arquivo não permitido.");
    }

    // filename must be random UUID
    $filename = uuidv4() . "." . $extension;
    $relativePath = "/storage/$filename";
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/storage/";
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            die("Falha ao criar o diretório.");
        }
    }

    if (move_uploaded_file($tmp, $targetPath)) {
        $photo_path = $relativePath;
    } else {
        die("Falha ao mover o arquivo enviado.");
    }
}

// Insert
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

$users = Connection::fetch("SELECT * FROM tb_contato");

foreach ($users as $key => $value) {
    foreach ($value as $key2 => $value2) {
        if (is_int($key2)) {
            unset($users[$key][$key2]);
        }
    }
}

var_dump($users);