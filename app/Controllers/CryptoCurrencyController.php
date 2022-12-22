<?php

namespace App\Controllers;

use App\Redirect;
use App\Services\CryptoCurrency\ListCryptoCurrencyService;
use App\Services\CryptoCurrency\TradeCryptoCurrencyService;
use App\Services\User\UserService;
use App\View;

class CryptoCurrencyController
{
    private ListCryptoCurrencyService $listCryptoCurrencyService;
    private TradeCryptoCurrencyService $tradeCryptoCurrencyService;
    private UserService $userService;

    public function __construct(ListCryptoCurrencyService $listCryptoCurrencyService,
                                TradeCryptoCurrencyService $tradeCryptoCurrencyService,
                                UserService $userService)
    {
        $this->listCryptoCurrencyService = $listCryptoCurrencyService;
        $this->tradeCryptoCurrencyService = $tradeCryptoCurrencyService;
        $this->userService = $userService;
    }

    public function index(): View
    {
        $single = $_GET['crypto'];
        $userOwnedCrypto = $this->userService->getUserCrypto($_SESSION['auth_id'],$single);
        $cryptoCurrencies = $this->listCryptoCurrencyService->execute(
            ['BTC', 'ETH', 'XRP', 'DOT', 'DOGE', 'LTC', 'BCH', 'ADA', 'BNB', 'SRM', 'LUNA', 'MATIC'],
            $single);

        if ($single != null)
            return View::render('single.twig', [
                'cryptoCurrencies' => $cryptoCurrencies->all(),
                'ownedCrypto' => $userOwnedCrypto
            ]);
        return View::render('main.twig', [
            'cryptoCurrencies' => $cryptoCurrencies->all()
        ]);
    }

    public function buy(): Redirect
    {
        $cryptoCurrencies = $this->listCryptoCurrencyService->execute([], $_POST['product']);
        $this->tradeCryptoCurrencyService->buy($cryptoCurrencies, $_POST['quantity']);
        return new Redirect('/?crypto=' . $_POST['product']);
    }

    public function sell(): Redirect
    {
        $cryptoCurrencies = $this->listCryptoCurrencyService->execute([], $_POST['product']);
        $this->tradeCryptoCurrencyService->sell($cryptoCurrencies, $_POST['quantity']);
        return new Redirect('/?crypto=' . $_POST['product']);
    }

    public function sellShort(): Redirect
    {
        $cryptoCurrencies = $this->listCryptoCurrencyService->execute([], $_POST['product']);
        $this->tradeCryptoCurrencyService->sellShort($cryptoCurrencies, $_POST['quantity']);
        return new Redirect('/?crypto=' . $_POST['product']);
    }
}
