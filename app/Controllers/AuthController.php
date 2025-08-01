<?php

namespace App\Controllers;

use App\DTO\UserRegisterDTO;
use App\Helpers\Response;
use App\Providers\Provider;
use App\Services\AuthService;

class AuthController
{
    public function __construct(
        public AuthService $authService = Provider::get(AuthService::class)
    ) {
    }
    public function login()
    {
        Response::view("login");
    }

    public function register()
    {
        Response::view("registro");
    }

    public function signup($dados)
    {
        try {
            $requiredFields = ['nome', 'login', 'email', 'senha', 'telefone'];

            foreach ($requiredFields as $field) {
                if (empty($dados[$field])) {
                    throw new \Exception("O campo '{$field}' é obrigatório.");
                }
            }

            $userDTO = new UserRegisterDTO(
                nome: $dados['nome'],
                login: $dados['login'],
                email: $dados['email'],
                senha: $dados['senha'],
                telefone: $dados['telefone'],
                foto: $_FILES["foto"] ?? null,
            );

            $user = $this->authService->registerUser($userDTO) ?? [];

            Response::json($user);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }
}