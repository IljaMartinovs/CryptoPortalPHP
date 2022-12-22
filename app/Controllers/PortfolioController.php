<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\User\UserService;
use App\Services\UserCrypto\ShowUserCrypto;
use App\CryptoValidation;
use App\View;

class PortfolioController
{
    private UserService $userService;
    private ShowUserCrypto $showUserCrypto;
    private CryptoValidation $validation;

    public function __construct(UserService    $userService,
                                ShowUserCrypto $showUserCrypto,
                                CryptoValidation     $validation)
    {
        $this->userService = $userService;
        $this->showUserCrypto = $showUserCrypto;
        $this->validation = $validation;
    }

    public function index(): View
    {
        //OWNED CRYPTO CURRENCIES
        $userOwnedCrypto = $this->userService->getUserCrypto($_SESSION['auth_id']);

        $cryptoSymbols = [];
        foreach ($userOwnedCrypto as $cryptoSymbol) {
            $cryptoSymbols[] = $cryptoSymbol["crypto_name"];
        }

        if (count($cryptoSymbols) == 0)
            return View::render('portfolio.twig', []);

        // ALL NEW INFO ABOUT OWNED CRYPTO CURRENCIES
        $userCrypto = $this->showUserCrypto->execute($cryptoSymbols, $_GET['crypto']);
        $portfolio = $userCrypto->all();


        //UPDATE CRYPTO_PRICE
        $this->userService->updatePrice($portfolio);

        //SUM OF OWNED CRYPTO
        $sum = 0;
        foreach ($userOwnedCrypto as $coin) {
            $sum += $coin['current_price'] * $coin["crypto_count"];
        }

        return View::render('portfolio.twig', [
            'owned' => $userOwnedCrypto,
            'moneyInCrypto' => $sum,
        ]);
    }

    public function sendCrypto(): Redirect
    {
        $userCrypto = $this->showUserCrypto->execute([], $_POST['symbol']);
        $portfolio = $userCrypto->all();
        $currentPrice = $portfolio[0]->getPrice();

        $this->validation->sendCrypto($_POST['symbol'], $_POST['amount'], $_POST['password'], $_POST['email']);
        if ($this->validation->validationFailed()) {
            return new Redirect('/portfolio');
        }
        $this->userService->sendCrypto($_POST['symbol'], $_POST['amount'], $_POST['email'], $currentPrice);
        return new Redirect('/portfolio');
    }

}