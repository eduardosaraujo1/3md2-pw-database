<?php

namespace App\Services;

use App\DTO\UserRegisterDTO;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = UserRepository::instance();
    }

    public function registerUser(UserRegisterDTO $data): User|null
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
            $dataArray['foto'] = StorageService::store($data->foto);
        }

        // Insert registry
        $this->userRepository->insert($dataArray);

        // read last result
        return $this->userRepository->getLatest();
    }

}