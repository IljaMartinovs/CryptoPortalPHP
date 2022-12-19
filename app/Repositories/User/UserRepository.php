<?php

namespace App\Repositories\User;

use App\Services\RegistrationServiceRequest;

interface UserRepository
{
    public function add(RegistrationServiceRequest $request): void;
    public function execute(): void;
}