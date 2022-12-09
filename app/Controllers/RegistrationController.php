<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\RegistrationService;
use App\Services\RegistrationServiceRequest;
use App\Template;
use App\Validation;

class RegistrationController
{
    public function show(): Template
    {
        return new Template('registration.twig');
    }

    public function store()
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