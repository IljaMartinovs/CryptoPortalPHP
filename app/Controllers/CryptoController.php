<?php

namespace App\Controllers;

use App\Services\IndexCryptoService;
use App\Template;

class CryptoController
{
    public function index(): Template
    {
        $crypto = (new IndexCryptoService())->execute();
        return new Template('main.twig', ['crypto' => $crypto]);
    }
}