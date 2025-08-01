<?php

namespace App\Services;

use App\DTO\SignInCredentialsDTO;
use App\DTO\UserRegisterDTO;
use App\Helpers\Response;
use App\Helpers\Result;
use App\Models\User;
use App\Providers\Provider;
use App\Repositories\UserRepository;
use Exception;

class AuthService
{
    public UserRepository $userRepository;
    public SessionService $session;
    public function __construct(
    ) {
        $this->userRepository = Provider::get(UserRepository::class);
        $this->session = Provider::get(SessionService::class);
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

    public function signInWithCredentials(SignInCredentialsDTO $credentials): ?User
    {
        $user = $this->userRepository->findByLoginAndPassword($credentials->login, $credentials->senha);

        if ($user) {
            $this->session->put("user_id", $user->id);
        }

        return $user;
    }

    public function signOut()
    {
        $this->session->remove('user_id');
    }
}