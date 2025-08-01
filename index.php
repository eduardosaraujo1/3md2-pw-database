<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\SessionService;
use App\Services\StorageService;

// Setup Service Provider
Provider::registerFactory(AuthController::class, fn() => new AuthController());
Provider::registerFactory(SessionService::class, fn() => new SessionService());
Provider::registerSingleton(UserRepository::class, new UserRepository());
Provider::registerSingleton(AuthService::class, new AuthService());
Provider::registerSingleton(DatabaseService::class, new DatabaseService());
Provider::registerSingleton(StorageService::class, new StorageService());

// Router
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
