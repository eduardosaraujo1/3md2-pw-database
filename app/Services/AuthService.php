<?php

namespace App\Services;

use App\DTO\LoginDTO;
use App\DTO\UserRegisterDTO;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(
        public UserRepository $userRepository,
        public Session $sessionService,
        public ImageStorageService $ImageStorageService
    ) {
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
            $dataArray['foto'] = $this->ImageStorageService->store($data->foto);
        }

        // Insert registry
        $this->userRepository->insert($dataArray);

        // read last result
        return $this->userRepository->getLatest();
    }

    public function signInWithCredentials(LoginDTO $credentials): ?User
    {
        $user = $this->userRepository->findByLoginAndPassword($credentials->login, $credentials->senha);

        if ($user) {
            $this->sessionService->put("user_id", $user->id);
        }

        return $user;
    }

    public function isSignedIn()
    {
        return $this->sessionService->has('user_id');
    }

    public function signOut()
    {
        $this->sessionService->remove('user_id');
    }
}