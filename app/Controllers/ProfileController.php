<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\CryptoCurrency\ListCryptoCurrencyService;
use App\Services\EditService;
use App\View;

class ProfileController
{
    public function show(): View
    {
//        $service = new ListCryptoCurrencyService();
//        $cryptoCurrencies = $service->findAll();
//        return View::render('profile.twig', [$cryptoCurrencies->all()]);
        return View::render('profile.twig', []);
    }

    public function addMoney(): Redirect
    {
        (new EditService())->changeUserMoney($_POST['amount']);
        return new Redirect('/profile');
    }
}