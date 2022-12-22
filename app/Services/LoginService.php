<?php

namespace App\Services;

use App\Repositories\User\UserRepository;

class LoginService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(): void
    {
        $this->userRepository->execute();
    }
}