<?php

namespace App\Controllers;

use App\Redirect;
use App\Template;
use App\Validation;
use App\Services\LoginService;

class LoginController
{
    public function show(): Template
    {
        return new Template('login.twig');
    }

    public function store(): Redirect
    {
        $validation = new Validation();
        $validation->loginValidate();

        if ($validation->validationFailed()) {
            return new Redirect('/login');
        }
        (new LoginService())->execute();
        return new Redirect('/');
    }
}