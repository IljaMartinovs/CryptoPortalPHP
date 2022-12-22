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
    private ListCryptoCurrencyService $listCryptoCurrencyService;
    private TradeCryptoCurrencyService $tradeCryptoCurrencyService;
    private UserService $userService;
    private ShowUserShorts $showUserShorts;

    public function __construct(ListCryptoCurrencyService  $listCryptoCurrencyService,
                                TradeCryptoCurrencyService $tradeCryptoCurrencyService,
                                UserService                $userService,
                                ShowUserShorts             $showUserShorts)
    {
        $this->listCryptoCurrencyService = $listCryptoCurrencyService;
        $this->tradeCryptoCurrencyService = $tradeCryptoCurrencyService;
        $this->userService = $userService;
        $this->showUserShorts = $showUserShorts;
    }

    public function index(): View
    {
        $userOwnedShorts = $this->userService->getUserShorts($_SESSION['auth_id']);

        $cryptoShorts = [];
        foreach ($userOwnedShorts as $cryptoShort) {
            $cryptoShorts[] = $cryptoShort["crypto_name"];
        }

        if (count($cryptoShorts) == 0)
            return View::render('orders.twig', []);

        // ALL NEW INFO ABOUT OWNED CRYPTO CURRENCIES
        $userShorts = $this->showUserShorts->execute($cryptoShorts);
        $portfolio = $userShorts->all();

        //UPDATE CRYPTO_PRICE
        $this->userService->updatePrice($portfolio);

        //SUM OF OWNED STOCKS
        $sum = 0;
        foreach ($userOwnedShorts as $short) {
            $sum += $short['current_price'] * $short["crypto_count"];
        }

        return View::render('orders.twig', [
            'shorts' => $userOwnedShorts,
            'moneyInShorts' => $sum,
        ]);
    }

    public function sellShorts(): Redirect
    {
        $cryptoCurrencies = $this->listCryptoCurrencyService->execute([], $_POST['symbol']);
        $this->tradeCryptoCurrencyService->closeShort($cryptoCurrencies, $_POST['amount']);
        return new Redirect('/orders');
    }
}