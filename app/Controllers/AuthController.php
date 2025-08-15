<?php

namespace App\Controllers;

use App\DTO\SignInCredentialsDTO;
use App\DTO\UserRegisterDTO;
use App\Helpers\Response;
use App\Providers\Provider;
use App\Services\AuthService;

class AuthController
{
    public AuthService $authService;
    public function __construct()
    {
        $this->authService = Provider::get(AuthService::class);
    }
    public function login()
    {
        if ($this->authService->isSignedIn()) {
            Response::redirect('/');
        } else {
            Response::view("login");
        }
    }

    public function signIn(array $dados)
    {
        try {
            // Form validation
            $required = ['login', 'senha'];

            foreach ($required as $field) {
                if (empty($dados[$field])) {
                    throw new \Exception("O campo '{$field}' é obrigatório.");
                }
            }

            // Logic
            $signInDTO = new SignInCredentialsDTO(
                login: $dados['login'],
                senha: $dados['senha']
            );

            $user = $this->authService->signInWithCredentials($signInDTO);

            if (!$user) {
                throw new \Exception("Usuário ou senha incorretos");
            }

            return Response::json(['status' => 'success', 'user' => $user]);
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function signOut()
    {
        $this->authService->signOut();
        return Response::redirect('/');
    }
}