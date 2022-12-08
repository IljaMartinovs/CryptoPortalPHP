<?php

namespace App\Services;

use App\Database;
use App\Validation;

class RegistrationService
{
    public function execute(RegistrationServiceRequest $request): void
    {
        Validation::validateName($request->getName());
        Validation::validateEmail($request->getEmail());
        Validation::validatePassword($request->getPassword(), $request->getRepeatedPassword());
        header('Location: /registration');
        if ($_SESSION['error'] == null) {
            Database::getConnection()->insert(
                'users',
                ['name' => $request->getName(), 'email' => $request->getEmail(), 'password' => $request->getPassword()]
            );
            header('Location: /');
        }
    }
}