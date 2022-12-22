<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\User\UserService;
use App\View;

class ProfileController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(): View
    {
        $transactions = $this->userService->getTransactions($_SESSION['auth_id']);
        return View::render('profile.twig', [
            'crypto' => $transactions,
        ]);
    }

    public function addMoney(): Redirect
    {
        $this->userService->changeUserMoney($_POST['amount']);
        return new Redirect('/profile');
    }

    public function search(): View
    {
        $transactions = $this->userService->getTransactions($_SESSION['auth_id'], $_POST['symbol']);
        return View::render('profile.twig', [
            'crypto' => $transactions,
        ]);
    }
}