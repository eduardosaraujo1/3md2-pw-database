<?php
require './app/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\ImageStorageService;
use App\Services\UserService;
use Core\Config\Configuration;
use Core\Config\ConnectionConfig;
use Core\Container\Container;
use Core\Database\MySQLConnection;
use Core\Database\SQLiteConnection;
use Core\Http\Request;
use Core\Services\Session;
use Core\Services\Database;
use Core\Services\Storage;

define('PROJECT_ROOT', __DIR__);

// Service Container
$container = Container::build(function (Container $container) {
    $db_driver = 0;
    $connectionConfig = new Configuration("database");
    $connection = match ($db_driver) {
        0 => SQLiteConnection::fromConfig(config: $connectionConfig),
        1 => MySQLConnection::fromConfig(config: $connectionConfig),
        default => throw new InvalidArgumentException("Unsupported database driver: $db_driver"),
    };
    $databaseService = new Database(
        connection: $connection
    );

    $storageService = new Storage();
    $oneHundredMB = 100 * 1024; // in kilobytes
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imageStorageService = new ImageStorageService(
        storage: $storageService,
        allowedTypes: $allowedTypes,
        maxFileSize: $oneHundredMB, // 100 MB max file size
    );

    $sessionService = new Session();
    $userRepository = new UserRepository(
        databaseService: $databaseService
    );
    $authService = new AuthService(
        userRepository: $userRepository,
        sessionService: $sessionService,
    );
    $userService = new UserService(
        userRepository: $userRepository,
        sessionService: $sessionService,
        imageStorageService: $imageStorageService
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
    $container->singleton(Database::class, $databaseService);
    $container->singleton(UserRepository::class, $userRepository);
    $container->singleton(AuthService::class, $authService);
    $container->singleton(UserService::class, $userService);
    $container->singleton(AuthController::class, $authController);
    $container->singleton(UserController::class, $userController);
});

// Request
$request = Request::createFromGlobals();

// Router
/** @var AuthController */
$authController = $container->make(AuthController::class);
/** @var UserController */
$userController = $container->make(UserController::class);

$uri = $_SERVER['REQUEST_URI'];
$method = $request->method();
$router = [
    // Auth
    '/login' => function () use ($authController, $request) {
        if ($request->method() === 'GET') {
            $authController->login();
        }
    },
    '/signin' => function () use ($authController, $request) {
        if ($request->method() === 'POST') {
            $authController->signin($request);
        }
    },
    '/signout' => function () use ($authController, $request) {
        if ($request->method() === 'GET') {
            $authController->signout();
        }
    },
    // User
    '/' => function () use ($userController, $request) {
        if ($request->method() === 'GET') {
            $userController->home();
        }
    },
    '/users' => function () use ($userController, $request) {
        if ($request->method() === 'GET') {
            $userController->index();
        }
    },
    '/users/store' => function () use ($userController, $request) {
        if ($request->method() === 'POST') {
            $userController->store($request);
        }
    },
    // Deprecated
    '/profile' => function () use ($userController, $request) {
        if ($request->method() === 'POST') {
            $userController->getProfile();
        }
    },
    '/profile/update' => function () use ($userController, $request) {
        if ($request->method() === 'POST') {
            $userController->updateProfile($request);
        }
    },
];

if (!array_key_exists($uri, $router)) {
    echo "<h1>Esse caminho não existe</h1>";
    die();
}

$callback = $router[$uri];
$response = $callback($request);

// End Router
