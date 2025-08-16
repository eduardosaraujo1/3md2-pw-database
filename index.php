<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\Session;
use App\Services\ImageStorageService;
use App\Services\UserService;
use Core\Container\Container;

// Service Container
$container = Container::build(function (Container $container) {
    $oneHundredMB = 100 * 1024; // in kilobytes
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imageStorageService = new ImageStorageService(
        allowedTypes: $allowedTypes,
        maxFileSize: $oneHundredMB, // 100 MB max file size
    );

    $sessionService = new Session();
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

    $container->singleton(Session::class, $sessionService);
    $container->singleton(ImageStorageService::class, $imageStorageService);
    $container->singleton(DatabaseService::class, $databaseService);
    $container->singleton(UserRepository::class, $userRepository);
    $container->singleton(AuthService::class, $authService);
    $container->singleton(UserService::class, $userService);
    $container->singleton(AuthController::class, $authController);
    $container->singleton(UserController::class, $userController);
});

// Router
/** @var AuthController */
$authController = $container->make(AuthController::class);
/** @var UserController */
$userController = $container->make(UserController::class);

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
