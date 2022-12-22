<?php

namespace App\Services;

use App\Repositories\User\UserRepository;

class RegistrationService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(RegistrationServiceRequest $request): void
    {
        $this->userRepository->add($request);
    }
}