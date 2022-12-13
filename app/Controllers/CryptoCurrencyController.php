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
//        echo "<pre>";
//        var_dump($cryptoCurrencies);die;
        return View::render('test.twig', [
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
