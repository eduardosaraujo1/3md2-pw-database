<?php
use Core\Http\Response;
use Core\Routing\Router;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use Core\Http\Request;

$router = app()->make(Router::class);
/** @var AuthController */
$authController = app()->make(AuthController::class);
/** @var UserController */
$userController = app()->make(UserController::class);

// Auth
$router->get('/login', function () use ($authController): Response {
    return $authController->login();
});
$router->post('/signin', function (Request $request) use ($authController): Response {
    return $authController->signin($request);
});
$router->get('/signout', function () use ($authController): Response {
    return $authController->signout();
});

// User
$router->get('/', function () use ($userController): Response {
    return $userController->home();
});
$router->get('/users', function () use ($userController): Response {
    return $userController->index();
});
$router->post('/users/store', function (Request $request) use ($userController): Response {
    return $userController->store($request);
});
$router->post('/users/update', function (Request $request) use ($userController): Response {
    return $userController->update($request);
});
$router->post('/users/destroy', function (Request $request) use ($userController): Response {
    return $userController->destroy($request);
});
