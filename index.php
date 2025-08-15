<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\SessionService;
use App\Services\ImageStorageService;
use App\Services\UserService;

// Service Container
function createServiceContainer(): void
{
    $oneHundredMB = 100 * 1024; // in kilobytes
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imageStorageService = new ImageStorageService(
        allowedTypes: $allowedTypes,
        maxFileSize: $oneHundredMB, // 100 MB max file size
    );

    $sessionService = new SessionService();
    $databaseService = new DatabaseService();

    $userRepository = new UserRepository($databaseService);
    $authService = new AuthService(
        userRepository: $userRepository,
        sessionService: $sessionService,
        ImageStorageService: $imageStorageService
    );
    $userService = new UserService(
        userRepository: $userRepository,
        sessionService: $sessionService
    );
    $authController = new AuthController(
        authService: $authService
    );
    $userController = new UserController(
        userService: $userService,
        authService: $authService
    );

    Provider::registerSingleton(SessionService::class, $sessionService);
    Provider::registerSingleton(ImageStorageService::class, $imageStorageService);
    Provider::registerSingleton(DatabaseService::class, $databaseService);
    Provider::registerSingleton(UserRepository::class, $userRepository);
    Provider::registerSingleton(AuthService::class, $authService);
    Provider::registerSingleton(UserService::class, $userService);
    Provider::registerSingleton(AuthController::class, $authController);
    Provider::registerSingleton(UserController::class, $userController);
}

createServiceContainer();
// End Service Container

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

// End Router
