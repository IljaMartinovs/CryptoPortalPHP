<?php

namespace App\Repositories\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Models\CryptoCurrency;
use GuzzleHttp\Client;

class CoinMarketCapCryptoCurrenciesRepository implements CryptoCurrenciesRepository
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => $_ENV['APIURL']]);
    }

    public function findAllBySymbols(array $symbols, ?string $single): CryptoCurrenciesCollection
    {
        if ($single != null)
            $symbols = $single;
        else
            $symbols = implode(',', $symbols);


        $response = $this->httpClient->request('GET', 'quotes/latest', [
            'headers' => [
                'Accepts' => 'application/json',
                'X-CMC_PRO_API_KEY' => $_ENV['APIKEY']
            ],
            'query' => [
                'symbol' => $symbols,
            ]
        ]);

        $response = json_decode($response->getBody()->getContents());

        $cryptoCurrencies = new CryptoCurrenciesCollection();
        foreach ($response->data as $currency) {
            $cryptoCurrencies->add(new CryptoCurrency(
                $currency->symbol,
                $currency->name,
                $currency->quote->USD->price,
                $currency->quote->USD->percent_change_1h,
                $currency->quote->USD->percent_change_24h,
                $currency->quote->USD->percent_change_7d
            ));
        }
        return $cryptoCurrencies;
    }

//    public function findAll(): CryptoCurrenciesCollection
//    {
//        $response = $this->httpClient->request('GET', 'listings/latest', [
//            'headers' => [
//                'Accepts' => 'application/json',
//                'X-CMC_PRO_API_KEY' => $_ENV['APIKEY']
//            ],
//
//        ]);
//
//        $response = json_decode($response->getBody()->getContents());
//
//        $cryptoCurrencies = new CryptoCurrenciesCollection();
//        foreach ($response->data as $currency) {
//            $cryptoCurrencies->add(new CryptoCurrency(
//                $currency->symbol,
//                $currency->name,
//                $currency->quote->USD->price,
//                $currency->quote->USD->percent_change_1h,
//                $currency->quote->USD->percent_change_24h,
//                $currency->quote->USD->percent_change_7d
//            ));
//        }
//        return $cryptoCurrencies;
//    }
}
