<?php

namespace App\Services;

use App\Domain\DTO\UserDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use Core\Services\Session;

class UserService
{
    public function __construct(
        public UserRepository $userRepository,
        public Session $sessionService,
        public ImageStorageService $imageStorageService
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

    public function createUser(UserDTO $data): User|null
    {
        // Prevent duplicates
        $duplicates = $this->userRepository->checkDuplicates(
            email: $data->email,
            login: $data->login
        );

        if ($duplicates['login']) {
            throw new \InvalidArgumentException("Login já está em uso");
        }
        if ($duplicates['email']) {
            throw new \InvalidArgumentException("Email já está em uso");
        }

        // Store photo
        $dataArray = $data->toArray();
        if ($data->foto) {
            $dataArray['foto'] = $this->imageStorageService->store($data->foto);
        }

        // Insert registry
        $this->userRepository->insert($dataArray);

        // read last result
        return $this->userRepository->getLatest();
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->all();
    }

    public function updateUser(int $id, UserDTO $data): User
    {
        $existingUser = $this->userRepository->findById((string) $id);

        if (!$existingUser) {
            throw new \Exception("Usuário não encontrado.");
        }

        try {
            $success = $this->userRepository->update($id, $data->toArray());

            if (!$success) {
                throw new \Exception("Falha ao atualizar o usuário.");
            }

            $updated = $this->userRepository->findById((string) $id);

            if (!$updated) {
                throw new \Exception("Erro ao recuperar o usuário atualizado.");
            }

            return $updated;
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'login')) {
                    throw new \Exception("Este login já está em uso. Por favor, escolha outro.");
                } elseif (str_contains($e->getMessage(), 'email')) {
                    throw new \Exception("Este e-mail já está cadastrado.");
                } else {
                    throw new \Exception("Dados duplicados: verifique os campos únicos.");
                }
            }

            throw $e;
        }
    }

}