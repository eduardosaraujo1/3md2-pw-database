<?php

namespace App\Services;

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
}