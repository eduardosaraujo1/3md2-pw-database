<?php

namespace App\Controllers;

use App\DTO\UserRegisterDTO;
use App\Helpers\Response;
use App\Providers\Provider;
use App\Services\AuthService;
use App\Services\UserService;
use PDOException;
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
            Response::view("home");
        } else {
            Response::redirect('/login');
        }
    }

    public function index()
    {
        try {
            $users = $this->userService->getAllUsers();
            $filtered = array_map(function ($user) {
                if (is_object($user)) {
                    $user = (array) $user;
                }
                return [
                    'id' => $user['id'] ?? null,
                    'nome' => $user['nome'] ?? null,
                    'login' => $user['login'] ?? null,
                    'email' => $user['email'] ?? null,
                    'telefone' => $user['telefone'] ?? null,
                ];
            }, $users);
            Response::json($filtered);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function store(array $dados)
    {
        try {
            // Form validation
            $requiredFields = ['nome', 'login', 'email', 'senha', 'telefone'];

            foreach ($requiredFields as $field) {
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
                foto: $_FILES["foto"] ?? null,
            );

            $user = $this->authService->registerUser($userDTO) ?? [];

            Response::json(['status' => 'success', 'user' => $user]);
        } catch (Exception $e) {
            Response::error($e->getMessage());
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

            Response::json($user);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }


    public function updateProfile(array $data)
    {
        try {
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

            Response::json(['status' => 'success', 'user' => $updatedUser]);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'login')) {
                    Response::error("Este login já está em uso. Por favor, escolha outro.");
                } elseif (str_contains($e->getMessage(), 'email')) {
                    Response::error("Este e-mail já está cadastrado.");
                } else {
                    Response::error("Dados duplicados: verifique os campos únicos.");
                }
            } else {
                Response::error("Erro de banco de dados: " . $e->getMessage());
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }

    }
}