<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Providers\Provider;
use App\Services\AuthService;
use App\Services\UserService;
use Exception;

class UserController
{
    public UserService $userService;
    public AuthService $authService;

    public function __construct()
    {
        $this->userService = Provider::get(UserService::class);
        $this->authService = Provider::get(AuthService::class);
    }
    public function home()
    {
        if ($this->authService->isSignedIn()) {
            Response::view("home");
        } else {
            Response::redirect('/login');
        }
    }

    public function getProfile()
    {
        try {
            $user = $this->userService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado");
            }

            Response::json($user);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}