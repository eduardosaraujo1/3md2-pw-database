<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\SessionService;
use App\Services\StorageService;
use App\Services\UserService;

// Setup Service Provider
Provider::registerFactory(AuthController::class, fn() => new AuthController());
Provider::registerFactory(UserController::class, fn() => new UserController());
Provider::registerSingleton(SessionService::class, new SessionService());
Provider::registerSingleton(UserRepository::class, new UserRepository());
Provider::registerSingleton(AuthService::class, new AuthService());
Provider::registerSingleton(UserService::class, new UserService());
Provider::registerSingleton(StorageService::class, new StorageService());

// Router
/** @var AuthController */
$authController = Provider::get(AuthController::class);
/** @var UserController */
$userController = Provider::get(UserController::class);

$uri = $_SERVER['REQUEST_URI'];
$router = [
    '/' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController->home();
        }
    },
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
    '/profile' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->getProfile();
        }
    },
    '/signup' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->signup($_POST);
        }
    },
    '/signin' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->signin($_POST);
        }
    },
    '/signout' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $authController->signout();
        }
    },
];

if (!array_key_exists($uri, $router)) {
    echo "<h1>Esse caminho não existe</h1>";
    die();
}

$callback = $router[$uri];

$response = $callback();
