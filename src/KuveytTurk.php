<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;

class KuveytTurk
{
    const KEY = 'kuveytturk';

    const NAME = 'Kuveyt Türk';

    const BASE_URL = 'https://www.kuveytturk.com.tr';

    const TOKEN_URL = 'https://www.kuveytturk.com.tr/finans-portali/';

    const REPLACES = [
        'ALT (gr)' => 'XAU/TRY',
    ];

    protected $items = [];

    protected function replace(string $symbol): string
    {
        if (strlen($symbol) === 3) {
            return $symbol.'/TRY';
        }

        return Service::replace(self::REPLACES, $symbol);
    }

    protected function getToken(): string
    {
        $client = new Client;
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Sec-Fetch-User' => '?1',
            'Upgrade-Insecure-Requests' => '1',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];
        $res = $client->get(self::TOKEN_URL, [
            'headers' => $headers,
        ]);

        $pattern = '/"fn-rltxhgrs":"(.*?)"/';
        preg_match($pattern, $res->getBody()->getContents(), $matches);

        return $matches[1];
    }

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.kuveytturk.com.tr/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'X-Bone-Language' => 'TR',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::BASE_URL.'/'.$this->getToken(), [
            'headers' => $headers,
        ]);

        $items = json_decode($res->getBody()->getContents(), true);
        $time = (new DateTime('now', new DateTimeZone('Europe/Istanbul')))->format('Y-m-d H:i:s');

        foreach ($items as $item) {
            if (! in_array($item['CurrencyCode'], ['TL', 'CAG (gr)', 'GMS (gr)', 'PLT (gr)', 'PLD (gr)', 'ZCeyrek'])) {
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => $this->replace($item['CurrencyCode']),
                    'buy' => Service::toFloat($item['BuyRate']),
                    'sell' => Service::toFloat($item['SellRate']),
                    'time' => $time,
                    'description' => $item['CurrencyDescription'],
                ];
            }
        }

        return $this->items;
    }
}
