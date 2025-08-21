<?php

namespace App\Services;

use App\Domain\DTO\UserDTO;
use App\Exceptions\QueryException;
use App\Exceptions\UserException;
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

    public function createUser(UserDTO $data): User|null
    {
        try {
            $user = new User(
                id: null,
                nome: $data->nome,
                login: $data->login,
                senha: $data->senha,
                email: $data->email,
                telefone: $data->telefone,
                foto: null,
            );

            // Store photo
            if ($data->foto) {
                $user->foto = $this->imageStorageService->store($data->foto);
            }

            if (!$this->userRepository->insert($user)) {
                throw new \Exception("Erro ao inserir usuário no banco de dados.");
            }

            // read last result
            return $this->userRepository->getLatest();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'login')) {
                    throw new UserException("Este login já está em uso.");
                } elseif (str_contains($e->getMessage(), 'email')) {
                    throw new UserException("Este e-mail já está em uso.");
                } else {
                    throw new UserException("Dados duplicados: verifique os campos únicos.");
                }
            }

            throw $e;
        }
    }

    public function updateUser(UserDTO $userDTO): User
    {
        if ($userDTO->id === null) {
            throw new UserException("ID do usuário é obrigatório para atualização.");
        }

        $existingUser = $this->userRepository->findById((string) $userDTO->id);

        if (!$existingUser) {
            throw new UserException("Usuário não encontrado.");
        }

        try {
            $user = new User(
                id: $userDTO->id,
                nome: $userDTO->nome,
                login: $userDTO->login,
                senha: $userDTO->senha,
                email: $userDTO->email,
                telefone: $userDTO->telefone,
                foto: $existingUser->foto
            );
            $success = $this->userRepository->update($user);

            if (!$success) {
                throw new \Exception("Falha ao atualizar o usuário.");
            }

            $updated = $this->userRepository->findById((string) $userDTO->id);

            if (!$updated) {
                throw new \Exception("Erro ao recuperar o usuário atualizado.");
            }

            return $updated;
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'login')) {
                    throw new UserException("Este login já está em uso.");
                } elseif (str_contains($e->getMessage(), 'email')) {
                    throw new UserException("Este e-mail já está cadastrado.");
                } else {
                    throw new UserException("Dados duplicados: verifique os campos únicos.");
                }
            }

            throw $e;
        }
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->all();
    }
}