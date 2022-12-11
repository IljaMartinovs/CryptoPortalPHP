<?php

namespace App\Services;

use App\Repositories\User\MySQLUserRepository;
use App\Repositories\User\UserRepository;

class RegistrationService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySQLUserRepository();
    }

    public function execute(RegistrationServiceRequest $request): void
    {
        $this->userRepository->add($request);
    }
}