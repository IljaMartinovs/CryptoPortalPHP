<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\EditService;
use App\Services\User\UserService;
use App\View;

class ProfileController
{
    public function show(): View
    {
        $service = new UserService();
        $transactions = $service->getTransactions($_SESSION['auth_id']);

        return View::render('profile.twig', [
            'crypto' => $transactions,
        ]);
    }

    public function addMoney(): Redirect
    {
        (new EditService())->changeUserMoney($_POST['amount']);
        return new Redirect('/profile');
    }

    public function search(): View
    {
        $service = new UserService();
        $transactions = $service->getTransactions($_SESSION['auth_id'],$_POST['symbol']);
        return View::render('profile.twig', [
            'crypto' => $transactions,
        ]);
    }
}