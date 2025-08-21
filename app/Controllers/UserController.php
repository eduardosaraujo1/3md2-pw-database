<?php

namespace App\Controllers;

use App\Domain\DTO\UserDTO;
use App\Exceptions\UserException;
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
    public function home(): Response
    {
        if ($this->authService->isSignedIn()) {
            return response()->view("home");
        } else {
            return response()->redirect('/login');
        }
    }

    public function index(): Response
    {
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
    }

    public function store(Request $request): Response
    {
        // Form validation
        $required = ['nome', 'login', 'email', 'senha', 'telefone'];
        $dados = $request->only([...$required, 'foto']);

        foreach ($required as $field) {
            if (empty($dados[$field])) {
                throw new UserException("O campo '{$field}' é obrigatório.");
            }
        }

        // Validate 'foto' field (must be nullable array)
        if (isset($dados['foto']) && !is_array($dados['foto'])) {
            // throw new Exception("O campo 'foto' deve ser um array ou nulo.");
            $dados['foto'] = null;
        }

        // Logic
        $userDTO = new UserDTO(
            nome: $dados['nome'],
            login: $dados['login'],
            email: $dados['email'],
            senha: $dados['senha'],
            telefone: $dados['telefone'],
            foto: $dados["foto"] ?? null,
        );

        $user = $this->userService->createUser($userDTO) ?? [];

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function update(Request $request): Response
    {
        // Form validation
        $required = ['id', 'nome', 'login', 'email', 'senha', 'telefone'];
        $dados = $request->only($required);

        foreach ($required as $field) {
            if (empty($dados[$field])) {
                throw new UserException("O campo '{$field}' é obrigatório.");
            }
        }

        // Validate 'id' is integer
        if (!is_int($dados['id'])) {
            throw new UserException("O campo 'id' deve ser um número inteiro.");
        }

        // Logic
        $userDTO = new UserDTO(
            id: (int) $dados['id'],
            nome: $dados['nome'] ?? '',
            login: $dados['login'] ?? '',
            email: $dados['email'] ?? '',
            senha: $dados['senha'] ?? '',
            telefone: $dados['telefone'] ?? '',
        );

        $user = $this->userService->updateUser($userDTO) ?? [];

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function destroy(Request $request): Response
    {
        return response()->json(['status' => 'success']);
    }

    // Descontinuado
    public function getProfile(): Response
    {
        try {
            $user = $this->authService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado");
            }

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function updateProfile(Request $request): Response
    {
        try {
            $data = $request->all();
            $user = $this->authService->getCurrentUser();

            if (!$user) {
                throw new Exception("Usuário não autenticado.");
            }

            $requiredFields = ['nome', 'login', 'email', 'senha', 'telefone'];

            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("O campo '{$field}' é obrigatório.");
                }
            }

            $userUpdateDTO = new UserDTO(
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