<?php

namespace App\Services;

use App\DTO\UserRegisterDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\SessionService;

class UserService
{
    public function __construct(
        public UserRepository $userRepository,
        public SessionService $sessionService
    ) {
    }

    public function getCurrentUser(): ?User
    {
        $user_id = $this->sessionService->get('user_id');

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