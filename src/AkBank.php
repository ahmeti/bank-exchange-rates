<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;

class AkBank
{
    const KEY = 'akbank';
    const NAME = 'Akbank';
    const BASE_URL = 'https://www.akbank.com';
    const DATA_URL = 'https://www.akbank.com/_vti_bin/AkbankServicesSecure/FrontEndServiceSecure.svc/GetCurrencyRates';

    protected $items = [];

    public function get(): array
    {
        $client = new Client();
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.akbank.com/doviz-kurlari',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"'
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        // what the hell!
        $replaces = [
            '\u000d\u000a' => '',
            '  ' => '',
            '\"' => '"',
            '{"GetCurrencyRatesResult":"{"cur": ' => '{"GetCurrencyRatesResult":{"cur":',
            '}"}' => '}}'
        ];
        $data = str_replace(array_keys($replaces), array_values($replaces), $res->getBody()->getContents());
        $data = json_decode($data, true);

        $time = $data['GetCurrencyRatesResult']['date'];
        $time = DateTime::createFromFormat('d.m.Y H:i:s', $time, new DateTimeZone('Europe/Istanbul'));
        $time = $time->format('Y-m-d H:i:s');

        foreach ($data['GetCurrencyRatesResult']['cur'] as $item) {
            if ($item['KurTuru'] == '08') {
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => $item['Title'] . '/TRY',
                    'buy' => $item['DovizAlis'],
                    'sell' => $item['DovizSatis'],
                    'time' => $time,
                    'description' => $item['Desc'],
                ];
            }
        }

        return $this->items;
    }
}
