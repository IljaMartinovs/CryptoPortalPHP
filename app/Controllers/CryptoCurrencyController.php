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
            ['BTC', 'ETH', 'XRP', 'DOT', 'DOGE', 'LTC', 'BCH', 'ADA', 'BNB', 'SRM','LUNA','MATIC'],
            $single
        );
        if($single != null){
            return View::render('single.twig', [
                'cryptoCurrencies' => $cryptoCurrencies->all()
            ]);
        }
        return View::render('main.twig', [
            'cryptoCurrencies' => $cryptoCurrencies->all()
        ]);
    }

    public function buy(): Redirect
    {
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute([], $_POST['product']);
        (new EditService())->buyCrypto($cryptoCurrencies, (float)$_POST['quantity']);
        return new Redirect('/?crypto=' . $_POST['product']);
    }

    public function sell(): Redirect
    {
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute([], $_POST['product']);
        (new EditService())->sellCrypto($cryptoCurrencies, (float)$_POST['quantity']);
        return new Redirect('/?crypto=' . $_POST['product']);
    }
}
