<?php

namespace App\Services;

use App\Domain\DTO\LoginDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use Core\Services\Session;

class AuthService
{
    public function __construct(
        public UserRepository $userRepository,
        public Session $sessionService
    ) {
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