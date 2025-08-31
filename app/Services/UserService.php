<?php

namespace App\Services;

use App\Domain\DTO\UserCreateDTO;
use App\Domain\DTO\UserUpdateDTO;
use App\Exceptions\QueryException;
use App\Exceptions\StorageException;
use App\Exceptions\UserException;
use App\Models\User;
use App\Repositories\UserRepository;
use Core\Services\Session;
use Exception;

class UserService
{
    public function __construct(
        public UserRepository $userRepository,
        public Session $sessionService,
        public ImageStorageService $imageStorageService
    ) {
    }

    /**
     * Cria um novo usuário no banco de dados. Pode retornar erros de conexão e erro de constraint duplicado
     * @param \App\Domain\DTO\UserCreateDTO $data
     * @throws \Exception
     * @throws \App\Exceptions\UserException
     * @return User|null
     */
    public function createUser(UserCreateDTO $data): User|null
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
                $fotoPath = $this->imageStorageService->store($data->foto);
                $user->setFoto($fotoPath);
            }

            if (!$this->userRepository->insert($user)) {
                throw new \Exception("Erro ao inserir usuário no banco de dados.");
            }

            // read last result
            return $this->userRepository->getLatest();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
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

    public function updateUser(UserUpdateDTO $userDTO): User
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
                nome: $userDTO->nome ?? $existingUser->nome,
                login: $userDTO->login ?? $existingUser->login,
                senha: $userDTO->senha ?? $existingUser->senha,
                email: $userDTO->email ?? $existingUser->email,
                telefone: $userDTO->telefone ?? $existingUser->telefone,
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
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
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

    public function deleteUser(int $id): void
    {
        if ($id === null) {
            throw new UserException("ID do usuário é obrigatório para exclusão.");
        }

        $this->userRepository->delete($id);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->all();
    }

    /**
     * Lê o armazenamento interno e consulta o banco de dados para obter a foto do usuário em forma binária
     *
     * O primeiro valor de retorno é o binário da imagem
     *
     * O segundo valor de retorno é o mime_type (utilizado pelo response_header) da imagem
     * @param int $user_id
     * @throws \App\Exceptions\UserException
     * @return string[]
     */
    /**
     * Returns the default user photo data and mime type
     * @return string[] Array containing [photo_data, mime_type]
     */
    private function getDefaultUserPhoto(): array
    {
        $path = realpath(PROJECT_ROOT . "/resources/assets/blank.png");
        $photo_data = file_get_contents($path);

        return [
            $photo_data,
            image_type_to_mime_type(IMAGETYPE_PNG)
        ];
    }

    public function getUserPhoto(int $user_id)
    {
        try {
            // Pegar caminho da foto a partir do ID do usuário
            $user = $this->userRepository->findById($user_id);
            if (!$user) {
                throw new UserException("Usuário de id $user_id não encontrado.");
            }

            // Usar ImageStorageService para pegar dados da imagem
            $photo_path = $user->foto ?? "";
            $photo_data = $this->imageStorageService->get($photo_path);

            // Retornar foto e mime_type da imagem a partir do caminho da foto (se for .png, mime type é image/png)
            if (str_ends_with($photo_path, ".png")) {
                $mime_type = image_type_to_mime_type(IMAGETYPE_PNG);
            } else if (str_ends_with($photo_path, ".jpg") || str_ends_with($photo_path, ".jpeg")) {
                $mime_type = image_type_to_mime_type(IMAGETYPE_JPEG);
            } else if (str_ends_with($photo_path, ".gif")) {
                $mime_type = image_type_to_mime_type(IMAGETYPE_GIF);
            } else {
                throw new UserException("Não foi possível ler a imagem armazenada: tipo não suportado. Contate o time de informática");
            }

            // Retornar
            return [
                $photo_data,
                $mime_type
            ];
        } catch (Exception $e) {
            return $this->getDefaultUserPhoto();
        }
    }
}