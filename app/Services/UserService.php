<?php

namespace App\Services;

use App\DTO\UserRegisterDTO;
use App\Models\User;
use App\Providers\Provider;
use App\Repositories\UserRepository;

class UserService
{
    public UserRepository $userRepository;
    public SessionService $session;
    public function __construct()
    {
        $this->userRepository = Provider::get(UserRepository::class);
        $this->session = Provider::get(SessionService::class);
    }

    public function getCurrentUser(): ?User
    {
        $user_id = $this->session->get('user_id');

        if (!$user_id) {
            return null;
        }

        $user = $this->userRepository->findById($user_id);

        return $user; // if user is null, response is also null
    }

    public function updateUser(int $id, UserRegisterDTO $data): User
    {
        $existingUser = $this->userRepository->findById($id);

        if (!$existingUser) {
            throw new \Exception("Usuário não encontrado.");
        }

        $success = $this->userRepository->update($id, $data->toArray());

        if (!$success) {
            throw new \Exception("Falha ao atualizar o usuário.");
        }

        $updated = $this->userRepository->findById($id);

        if (!$updated) {
            throw new \Exception("Erro ao recuperar o usuário atualizado.");
        }

        return $updated;
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->all();
    }
}