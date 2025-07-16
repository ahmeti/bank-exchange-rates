<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;

class IsBankasi
{
    const KEY = 'isbankasi';

    const NAME = 'İş Bankası';

    const BASE_URL = 'https://www.isbank.com.tr';

    const DATA_URL = 'https://www.isbank.com.tr/_vti_bin/DV.Isbank/PriceAndRate/PriceAndRateService.svc/GetFxRates';

    protected $items = [];

    public function get(): array
    {
        $now = new DateTime('now', new DateTimeZone('Europe/Istanbul'));

        $client = new Client;
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.isbank.com.tr/doviz-kurlari',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];
        $query = [
            'Lang' => 'tr',
            'fxRateType' => 'IB',
            'date' => $now->format('Y-m-d'),
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
            'query' => $query,
        ]);

        $items = json_decode($res->getBody()->getContents(), true);

        foreach ($items['Data'] as $item) {
            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => $item['code'].'/TRY',
                'buy' => $item['fxRateBuy'],
                'sell' => $item['fxRateSell'],
                'time' => $now->format('Y-m-d H:i:s'),
                'description' => $item['description'],
            ];
        }

        return $this->items;
    }
}
