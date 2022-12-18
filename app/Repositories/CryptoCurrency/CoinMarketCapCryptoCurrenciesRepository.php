<?php

namespace App\Repositories\CryptoCurrency;

use App\Models\Collection\CryptoCurrenciesCollection;
use App\Models\CryptoCurrency;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CoinMarketCapCryptoCurrenciesRepository implements CryptoCurrenciesRepository
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => $_ENV['APIURL']]);
    }

    public function findAllBySymbols(array $symbols, ?string $single): CryptoCurrenciesCollection
    {

//        if ($single != null)
//            $symbols = $single;
//        else
//            $symbols = implode(',', $symbols);

//        $response = $this->httpClient->request('GET', 'quotes/latest', [
//            'headers' => [
//                'Accepts' => 'application/json',
//                'X-CMC_PRO_API_KEY' => $_ENV['APIKEY']
//            ],
//            'query' => [
//                'symbol' => $symbols,
//            ]
//        ]);
//
//        $response = json_decode($response->getBody()->getContents());


        $response = $this->fetch($symbols, $single);
        $cryptoCurrencies = new CryptoCurrenciesCollection();
        $info = $this->fetch($symbols, $single,'info');

        foreach ($response->data as $currency) {
            $currency->logo = $info->data->{$currency->symbol}->logo;
            $cryptoCurrencies->add(new CryptoCurrency(
                $currency->symbol,
                $currency->name,
                $currency->quote->USD->price,
                $currency->quote->USD->percent_change_1h,
                $currency->quote->USD->percent_change_24h,
                $currency->quote->USD->percent_change_7d,
                $currency->logo
            ));
        }
        return $cryptoCurrencies;
    }

    private function fetch(array $symbols, ?string $single,string $url='quotes/latest')
    {

        if ($single != null)
            $symbols = $single;
        else
            $symbols = implode(',', $symbols);

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Accepts' => 'application/json',
                'X-CMC_PRO_API_KEY' => $_ENV['APIKEY']
            ],
            'query' => [
                'symbol' => $symbols,
            ]
        ]);

        return  json_decode($response->getBody()->getContents());
    }
}