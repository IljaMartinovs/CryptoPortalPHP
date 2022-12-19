<?php

namespace App\Services;

use App\Repositories\User\MySQLUserRepository;
use App\Repositories\User\UserRepository;

class LoginService
{

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySQLUserRepository();
    }

    public function execute(): void
    {
        $this->userRepository->execute();
    }

}