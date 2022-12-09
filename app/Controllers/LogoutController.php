<?php

namespace App\Controllers;

use App\Redirect;

class LogoutController
{
    public function logout(): Redirect
    {
        unset($_SESSION['auth_id']);
        return new Redirect('/');
    }
}