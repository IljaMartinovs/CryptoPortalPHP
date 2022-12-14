<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\EditService;
use App\View;

class ProfileController
{
    public function show(): View
    {
        return View::render('profile.twig', []);
    }

    public function addMoney(): Redirect
    {
        (new EditService())->changeUserMoney($_POST['amount']);
        return new Redirect('/profile');
    }
}