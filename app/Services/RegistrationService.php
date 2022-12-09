<?php

namespace App\Services;

use App\Database;

class RegistrationService
{
    public function execute(RegistrationServiceRequest $request): void
    {
        Database::getConnection()->insert(
            'users', [
            'name' => $request->getName(),
            'email' => filter_var($request->getEmail(), FILTER_SANITIZE_EMAIL),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT)
        ]);
    }
}