<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Providers\Provider;
use App\Services\UserService;
use Exception;

class UserController
{
    public UserService $userService;

    public function __construct()
    {
        $this->userService = Provider::get(UserService::class);
    }
    public function home()
    {
        Response::view("home");
    }

    public function getProfile()
    {
        try {
            $user = $this->userService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado");
            }

            Response::json($user->toJson());
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}