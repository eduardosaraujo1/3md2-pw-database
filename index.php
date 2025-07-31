<?php
require './app/autoload.php';

use App\Controllers\AuthController;

$authController = new AuthController();

$uri = $_SERVER['REQUEST_URI'];
$router = [
    '/login' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $authController->login();
        }
    },
    '/register' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $authController->register();
        }
    },
    '/signup' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->signup($_POST);
        }
    }
];

if (!array_key_exists($uri, $router)) {
    echo "<h1>Esse caminho não existe</h1>";
    die();
}

$callback = $router[$uri];

$response = $callback();
