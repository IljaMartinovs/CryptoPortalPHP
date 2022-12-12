<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\CryptoCurrency\ListCryptoCurrencyService;
use App\Services\EditService;
use App\View;

class CryptoCurrencyController
{
    public function index(): View
    {
        $single = $_GET['crypto'];
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute(
            ['BTC', 'ETH', 'XRP', 'DOT', 'DOGE', 'LTC', 'BCH', 'ADA', 'BNB', 'SRM'],
            $single
        );
        return View::render('main.twig', [
            'cryptoCurrencies' => $cryptoCurrencies->all()
        ]);
    }

    public function buy(): Redirect
    {
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute([], $_POST['product']);
        (new EditService())->buyCrypto($cryptoCurrencies, (float)$_POST['quantity']);
        return new Redirect('/');
    }

    public function sell(): Redirect
    {
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute([], $_POST['product']);
        (new EditService())->sellCrypto($cryptoCurrencies, (float)$_POST['quantity']);
        return new Redirect('/');
    }
}
