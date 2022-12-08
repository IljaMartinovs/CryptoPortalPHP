<?php

namespace App\Repositories;

use App\CryptoCollection;
use App\Models\Crypto;
use App\Services\CryptoDataService;

class CryptoApiCryptoRepository implements CryptoRepository
{
    public function getCrypto(string $start, string $limit, string $convert = 'USD'): CryptoCollection
    {
        $cryptoResponse = (new CryptoDataService())->getCrypto(1,12);
        $cryptos = new CryptoCollection();

        foreach ($cryptoResponse as $row) {
            $cryptos->addCrypto(new Crypto(
                $row['name'],
                $row['symbol'],
                $row['quote']['USD']['price'],
                $row['quote']['USD']['percent_change_24h']
            ));

        }
        return $cryptos;
    }
}