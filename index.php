<?php
define('PROJECT_ROOT', __DIR__);
require 'core/autoload.php';
require 'core/functions.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use Core\Http\Request;

// Bootstrap app service providers
app()->bootstrap();

// Request
$request = app()->make(Request::class);

// Router
/** @var AuthController */
$authController = app()->make(AuthController::class);
/** @var UserController */
$userController = app()->make(UserController::class);

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
