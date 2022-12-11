<?php

namespace App\Repositories\User;

use App\Database;
use App\Services\RegistrationServiceRequest;

class MySQLUserRepository implements UserRepository
{
    public function add(RegistrationServiceRequest $request): void
    {
        Database::getConnection()->insert(
            'users', [
            'name' => $request->getName(),
            'email' => filter_var($request->getEmail(), FILTER_SANITIZE_EMAIL),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            'money' => $request->getMoney()
        ]);
    }
}