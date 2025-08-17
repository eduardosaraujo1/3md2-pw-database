<?php

namespace App\Controllers;

use App\Domain\DTO\LoginDTO;
use App\Services\AuthService;
use Core\Http\Request;
use Core\Http\Response;

class AuthController
{
    public function __construct(
        public AuthService $authService

    ) {
    }
    public function login()
    {
        if ($this->authService->isSignedIn()) {
            return response()->redirect('/');
        } else {
            return response()->view("login");
        }
    }

    public function signIn(Request $request)
    {
        try {
            // Form validation
            $required = ['login', 'senha'];
            $dados = $request->only($required);

            foreach ($required as $field) {
                if (empty($dados[$field])) {
                    throw new \Exception("O campo '{$field}' é obrigatório.");
                }
            }

            // Logic
            $signInDTO = new LoginDTO(
                login: $dados['login'],
                senha: $dados['senha']
            );

            $user = $this->authService->signInWithCredentials($signInDTO);

            if (!$user) {
                throw new \Exception("Usuário ou senha incorretos");
            }

            return response()->json(['status' => 'success', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function signOut()
    {
        $this->authService->signOut();
        return response()->redirect('/');
    }
}