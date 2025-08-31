<?php

namespace App\Controllers;

use App\Domain\DTO\UserCreateDTO;
use App\Domain\DTO\UserUpdateDTO;
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
        $filtered = array_map(fn(User $user) => $user->toArray(), $users);

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
        $userDTO = new UserCreateDTO(
            nome: $dados['nome'],
            login: $dados['login'],
            email: $dados['email'],
            senha: $dados['senha'],
            telefone: $dados['telefone'],
            foto: $dados["foto"] ?? null,
        );

        $user = $this->userService->createUser($userDTO);

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function update(Request $request): Response
    {
        // Form validation
        $required = ['id'];
        $allowed = ['id', 'nome', 'login', 'email', 'senha', 'telefone'];
        $dados = $request->only($allowed);

        foreach ($required as $field) {
            if (empty($dados[$field])) {
                throw new UserException("O campo '{$field}' é obrigatório.");
            }
        }

        // Validate 'id' is numeric
        if (filter_var($dados['id'], FILTER_VALIDATE_INT) === false) {
            throw new UserException("O campo 'id' deve ser um número inteiro.");
        }

        // Logic
        $userDTO = new UserUpdateDTO(
            id: (int) $dados['id'],
            nome: $dados['nome'] ?? null,
            login: $dados['login'] ?? null,
            email: $dados['email'] ?? null,
            senha: $dados['senha'] ?? null,
            telefone: $dados['telefone'] ?? null,
        );

        $user = $this->userService->updateUser($userDTO);

        if (!$user) {
            throw new Exception('Erro ao atualizar usuário.');
        }

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function destroy(Request $request): Response
    {
        $required = ['id'];
        $dados = $request->only($required);

        foreach ($required as $field) {
            if (empty($dados[$field])) {
                throw new UserException("O campo '{$field}' é obrigatório.");
            }
        }

        // Validate 'id' is integer
        if (filter_var($dados['id'], FILTER_VALIDATE_INT) === false) {
            throw new UserException("O campo 'id' deve ser um número inteiro.");
        }

        $this->userService->deleteUser((int) $dados['id']);

        return response()->json(['status' => 'success']);
    }

    public function profile_image(Request $request)
    {
        // Validate Data
        $allowed = ['id'];
        $dados = $request->only($allowed);

        // Validate 'id' is parsable as integer
        if (isset($dados['id']) && filter_var($dados['id'], FILTER_VALIDATE_INT) === false) {
            throw new UserException("O campo 'id' deve ser um número inteiro.");
        }

        // Get photo
        try {
            $id = $dados['id'] ?? -1;
            [$photo_data, $mime_type] = $this->userService->getUserPhoto((int) $id);

            return new Response(
                body: $photo_data,
                headers: [
                    'Content-Type' => $mime_type
                ]
            );
        } catch (Exception $e) {
            error_log($e);

            return "";
        }
    }
}