<?php
define('PROJECT_ROOT', __DIR__);
require 'core/autoload.php';
require 'core/functions.php';

use Core\Http\Kernel;
use Core\Http\Response;
use Core\Routing\Router;
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

$router = app()->make(Router::class);

// Auth
$router->get('/login', function () use ($authController) {
    return $authController->login();
});
$router->post('/signin', function (Request $request) use ($authController) {
    return $authController->signin($request);
});
$router->get('/signout', function () use ($authController) {
    return $authController->signout();
});

// User
$router->get('/', function () use ($userController): Response {
    return $userController->home();
});
$router->get('/users', function () use ($userController) {
    return $userController->index();
});
$router->post('/users/store', function (Request $request) use ($userController) {
    return $userController->store($request);
});

// Deprecated
$router->get('/profile', function () use ($userController): Response {
    return $userController->getProfile();
});
$router->post('/profile/update', function (Request $request) use ($userController): Response {
    return $userController->updateProfile($request);
});

// Request
$request = app()->make(Request::class);

// Kernel
$kernel = app()->make(Kernel::class);

// Respond
$response = $kernel->handle($request);
$response->send();