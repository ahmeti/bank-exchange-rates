<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use GuzzleHttp\Client;

class CepteTeb
{
    const KEY = 'cepteteb';

    const NAME = 'Cepte TEB';

    const BASE_URL = 'https://www.cepteteb.com.tr';

    const DATA_URL = 'https://www.cepteteb.com.tr/services/GetGunlukDovizKur';

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Content-Length' => '0',
            'Origin' => 'https://www.cepteteb.com.tr',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.cepteteb.com.tr/doviz-kurlari',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        $items = json_decode($res->getBody()->getContents(), true);

        foreach ($items['result'] as $item) {

            $time = DateTime::createFromFormat('d/m/Y H:i:s', $item['fiyatZaman'], Service::timeZone())->format('Y-m-d H:i:s');

            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => $item['paraKodu'].'/TRY',
                'buy' => $item['tebAlis'],
                'sell' => $item['tebSatis'],
                'time' => $time,
                'description' => $item['paraAdi'],
            ];
        }

        return $this->items;
    }
}
