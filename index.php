<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use App\Services\AuthService;
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
    // Auth
    '/login' => function () use ($authController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $authController->login();
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
    // User
    '/' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController->home();
        }
    },
    '/users' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController->index();
        }
    },
    '/users/store' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->store($_POST);
        }
    },
    // Deprecated
    '/profile' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->getProfile();
        }
    },
    '/profile/update' => function () use ($userController) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->updateProfile($_POST);
        }
    },
];

if (!array_key_exists($uri, $router)) {
    echo "<h1>Esse caminho não existe</h1>";
    die();
}

$callback = $router[$uri];

$response = $callback();
