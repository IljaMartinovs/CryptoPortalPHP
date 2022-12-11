<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\RegistrationService;
use App\Services\RegistrationServiceRequest;
use App\Validation;
use App\View;

class RegistrationController
{
    public function show(): View
    {
        return View::render('registration.twig', []);
    }

    public function store(): Redirect
    {
        $validation = new Validation();
        $validation->validate();
        if ($validation->validationFailed()) {
            return new Redirect('/registration');
        }

        $registerService = new RegistrationService();
        $registerService->execute(
            new RegistrationServiceRequest(
                $_POST['name'],
                $_POST['email'],
                $_POST['password']
            )
        );
        return new Redirect('/');
    }
}