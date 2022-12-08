<?php

namespace App\Controllers;

class LogoutController
{
    public function logout()
    {
        // unset($_SESSION['name']);
        session_destroy();
        header('Location: /');
    }
}