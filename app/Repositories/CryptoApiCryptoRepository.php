<?php

namespace App\Repositories;

use App\CryptoCollection;
use App\Models\Crypto;
use App\Services\CryptoDataService;

class CryptoApiCryptoRepository implements CryptoRepository
{
    public function getCrypto(): CryptoCollection
    {
        $cryptoResponse = (new CryptoDataService())->getCrypto();
        $cryptos = new CryptoCollection();

        foreach ($cryptoResponse->data as $coin) {
            $cryptos->addCrypto(new Crypto(
                $coin->name,
                $coin->symbol,
                $coin->quote->USD->price,
                $coin->quote->USD->percent_change_24h
            ));
        }
        return $cryptos;
    }
}