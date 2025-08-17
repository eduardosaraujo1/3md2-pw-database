<?php

use Core\Http\Kernel;
define('PROJECT_ROOT', __DIR__);
require 'core/autoload.php';
require 'core/functions.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use Core\Http\Request;

// Bootstrap app service providers
app()->bootstrap();

// Router
/** @var AuthController */
$authController = app()->make(AuthController::class);
/** @var UserController */
$userController = app()->make(UserController::class);

$router = [
    // Auth
    '/login' => function (Request $request) use ($authController) {
        if ($request->method() === 'GET') {
            return $authController->login();
        }
    },
    '/signin' => function (Request $request) use ($authController) {
        if ($request->method() === 'POST') {
            return $authController->signin($request);
        }
    },
    '/signout' => function (Request $request) use ($authController) {
        if ($request->method() === 'GET') {
            return $authController->signout();
        }
    },
    // User
    '/' => function (Request $request) use ($userController) {
        if ($request->method() === 'GET') {
            return $userController->home();
        }
    },
    '/users' => function (Request $request) use ($userController) {
        if ($request->method() === 'GET') {
            return $userController->index();
        }
    },
    '/users/store' => function (Request $request) use ($userController) {
        if ($request->method() === 'POST') {
            return $userController->store($request);
        }
    },
    // Deprecated
    '/profile' => function (Request $request) use ($userController) {
        if ($request->method() === 'POST') {
            return $userController->getProfile();
        }
    },
    '/profile/update' => function (Request $request) use ($userController) {
        if ($request->method() === 'POST') {
            return $userController->updateProfile($request);
        }
    },
];

// Request
$request = app()->make(Request::class);

// Kernel
$kernel = new Kernel($router);
$response = $kernel->handle($request);

// Respond
$response->send();