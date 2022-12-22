<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\RegistrationService;
use App\Services\RegistrationServiceRequest;
use App\Validation;
use App\View;

class RegistrationController
{
    private RegistrationService $registrationService;
    private Validation $validation;

    public function __construct(RegistrationService $registrationService,
                                Validation $validation)
    {
        $this->registrationService = $registrationService;
        $this->validation = $validation;
    }

    public function show(): View
    {
        return View::render('registration.twig', []);
    }

    public function store(): Redirect
    {
        $this->validation->validate();
        if ($this->validation->validationFailed()) {
            return new Redirect('/registration');
        }
        $this->registrationService->execute(
            new RegistrationServiceRequest(
                $_POST['name'],
                $_POST['email'],
                $_POST['password']
            )
        );
        return new Redirect('/');
    }
}