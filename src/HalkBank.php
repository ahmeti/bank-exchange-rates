<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;

class HalkBank
{
    const KEY = 'halkbank';

    const NAME = 'Halkbank';

    const BASE_URL = 'https://www.halkbank.com.tr';

    const DATA_URL = 'https://webapi.halkbank.com.tr/api/MarketInformation/';

    const REPLACES = [
        'USD' => 'USD/TRY',
        'EUR' => 'EUR/TRY',
        'Altın (995/1000)' => 'XAU/TRY',
    ];

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Origin' => 'https://www.halkbank.com.tr',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.halkbank.com.tr/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        $json = json_decode($res->getBody()->getContents(), true);
        $time = str_replace('T', ' ', $json['data']['lastModifiedDate']);

        foreach ($json['data']['exchangeItems'] as $item) {
            if (in_array($item['name'], ['USD', 'EUR', 'Altın (995/1000)'])) {
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => Service::replace(self::REPLACES, $item['name']),
                    'buy' => $item['buying'],
                    'sell' => $item['selling'],
                    'time' => $time,
                    'description' => null,
                ];
            }
        }

        return $this->items;
    }
}
