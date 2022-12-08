<?php

namespace App\Controllers;

use App\Database;
use App\Services\RegistrationService;
use App\Services\RegistrationServiceRequest;
use App\Template;

class RegistrationController
{
    public function show(): Template
    {
        return new Template('registration.twig');
    }

    public function store()
    {
        $registrationService = new RegistrationService();
        $registrationService->execute(
            new RegistrationServiceRequest(
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['confirm-password']
            )
        );
    }
}