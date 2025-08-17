<?php

namespace App\Controllers;

use App\Domain\DTO\UserRegisterDTO;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Core\Http\Request;
use Core\Http\Response;
use Exception;

class UserController
{

    public function __construct(
        public UserService $userService,
        public AuthService $authService
    ) {
    }
    public function home()
    {
        if ($this->authService->isSignedIn()) {
            return response()->view("home");
        } else {
            return response()->redirect('/login');
        }
    }

    public function index(): Response
    {
        try {
            $users = $this->userService->getAllUsers();
            $filtered = array_map(function (User $user) {
                return [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'login' => $user->login,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                ];
            }, $users);
            return response()->json($filtered);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Form validation
            $required = ['nome', 'login', 'email', 'senha', 'telefone'];
            $dados = $request->only([...$required, 'foto']);

            foreach ($required as $field) {
                if (empty($dados[$field])) {
                    throw new Exception("O campo '{$field}' é obrigatório.");
                }
            }

            // Logic
            $userDTO = new UserRegisterDTO(
                nome: $dados['nome'],
                login: $dados['login'],
                email: $dados['email'],
                senha: $dados['senha'],
                telefone: $dados['telefone'],
                foto: $dados["foto"] ?? null,
            );

            $user = $this->userService->createUser($userDTO) ?? [];

            response()->json(['status' => 'success', 'user' => $user]);
        } catch (Exception $e) {
            response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Descontinuado
    public function getProfile()
    {
        try {
            $user = $this->userService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado");
            }

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $data = $request->all();
            $user = $this->userService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado.");
            }

            $requiredFields = ['nome', 'login', 'email', 'senha', 'telefone'];

            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("O campo '{$field}' é obrigatório.");
                }
            }

            $userUpdateDTO = new UserRegisterDTO(
                nome: $data['nome'],
                login: $data['login'],
                email: $data['email'],
                senha: $data['senha'],
                telefone: $data['telefone'],
            );

            $updatedUser = $this->userService->updateUser($user->id, $userUpdateDTO);

            return response()->json(['status' => 'success', 'user' => $updatedUser]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}