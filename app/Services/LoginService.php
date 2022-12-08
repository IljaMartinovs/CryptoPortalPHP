<?php

namespace App\Services;

use App\Database;
use App\Validation;

class LoginService
{
    public function execute(LoginServiceRequest $request): void
    {
        Validation::validateUser($request->getEmail(), $request->getPassword());
        header('Location: /login');
        if ($_SESSION['error'] == null) {
            header('Location: /');
        }
    }
}