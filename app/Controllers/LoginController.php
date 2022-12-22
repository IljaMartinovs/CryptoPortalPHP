<?php

namespace App\Controllers;

use App\Redirect;
use App\Validation;
use App\Services\LoginService;
use App\View;

class LoginController
{
    private LoginService $loginService;
    private Validation $validation;

    public function __construct(LoginService $loginService,Validation $validation)
    {
        $this->loginService = $loginService;
        $this->validation = $validation;
    }

    public function show(): View
    {
        return View::render('login.twig', []);
    }

    public function store(): Redirect
    {
        $this->validation->loginValidate();
        if ( $this->validation->validationFailed()) {
            return new Redirect('/login');
        }
        $this->loginService->execute();
        return new Redirect('/');
    }
}