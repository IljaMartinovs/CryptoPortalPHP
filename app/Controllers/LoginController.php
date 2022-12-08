<?php

namespace App\Controllers;

use App\Services\LoginService;
use App\Services\LoginServiceRequest;
use App\Template;

class LoginController
{
    public function show(): Template
    {
        return new Template('login.twig');
    }

    public function store()
    {
        $loginService = new LoginService();
        $loginService->execute(
            new LoginServiceRequest(
                $_POST['email'],
                $_POST['password']
            )
        );
    }
}