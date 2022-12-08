<?php

namespace App\Controllers;

use App\Template;

class ProfileController
{
    public function show(): Template
    {
        return new Template('profile.twig');
    }
}