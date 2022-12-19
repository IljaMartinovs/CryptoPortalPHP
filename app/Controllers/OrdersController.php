<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\CryptoCurrency\ListCryptoCurrencyService;
use App\Services\CryptoCurrency\TradeCryptoCurrencyService;
use App\Services\User\UserService;
use App\Services\UserShorts\ShowUserShorts;
use App\View;

class OrdersController
{
    public function index(): View
    {
        $service = new UserService();
        $userOwnedShorts = $service->getUserShorts($_SESSION['auth_id']);


        $cryptoShorts = [];
        foreach ($userOwnedShorts as $cryptoShort) {
            $cryptoShorts[] = $cryptoShort["crypto_name"];
        }


        if(count($cryptoShorts) == 0 )
            return View::render('orders.twig',[]);


        // ALL NEW INFO ABOUT OWNED CRYPTO CURRENCIES
        $service = new ShowUserShorts();
        $userShorts = $service->execute($cryptoShorts);
        $portfolio = $userShorts->all();



        //UPDATE CRYPTO_PRICE
        $service = new UserService();
        $service->updatePrice($portfolio);

        //SUM OF OWNED STOCKS
        $sum = 0;
        foreach ($userOwnedShorts as $short){
            $sum += $short['current_price']*$short["crypto_count"];
        }

        return View::render('orders.twig', [
            'shorts'=>$userOwnedShorts,
            'moneyInShorts' => $sum,
        ]);
    }

    public function sellShorts(): Redirect
    {
        $service = new ListCryptoCurrencyService();
        $cryptoCurrencies = $service->execute([], $_POST['symbol']);
        (new TradeCryptoCurrencyService())->closeShort($cryptoCurrencies);
        return new Redirect('/orders');
    }
}