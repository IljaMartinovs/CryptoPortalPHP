<?php

namespace App\Controllers;

use App\Redirect;
use App\Template;
use App\Validation;
use App\Services\LoginService;
use App\View;

class LoginController
{
    public function show(): View
    {
        return View::render('login.twig', []);
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