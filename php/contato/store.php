<?php
require '../core/autoload.php';
use Core\Database\Connection;

function uuidv4()
{
  $data = random_bytes(16);

  $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

Connection::create([
    "host" => "localhost",
    "username" => "root",
    "password" => "root",
    "database" => "learning",
    "port" => "3306"
]);

// Collect
$nome = $_POST["nome"];
$login = $_POST["login"];
$email = $_POST["email"];
$senha = $_POST["senha"];
$telefone = $_POST["telefone"];

// store uploaded photo
$photo_path = null;

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
    $foto = $_FILES["foto"];
    $tmp = $foto["tmp_name"];
    $originalName = $foto["name"];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
    if (!in_array($extension, $allowed)) {
        die("Tipo de arquivo não permitido.");
    }

    $filename = uuidv4() . "." . $extension;

    $relativePath = "/storage/contato/foto/" . $filename;
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/storage/contato/foto/";
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
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

/**
[WWWWWWWWWWWWWWWW]
f(i) = offset + i - length
-length <= f(i) <= 
0 <= offset <= length-1
[WWWWWWWWWWWWWWWW]
f(i) = 15 + i - offset
<= f(i) <=
<= offset <= 
 
 */