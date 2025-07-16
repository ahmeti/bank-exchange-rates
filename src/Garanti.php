<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class Garanti
{
    const KEY = 'garanti';

    const NAME = 'Garanti';

    const BASE_URL = 'https://www.garantibbva.com.tr';

    const DATA_URL = 'https://customers.garantibbva.com.tr/digital-public/currency-convertor-public/v2/currency-convertor/currency-list-detail';

    const REPLACES = [
        '/TL' => '/TRY',
        'TL/' => 'TRY/',
        '/ALT' => '/XAU',
        'ALT/' => 'XAU/',
    ];

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'en,tr;q=0.9,en-US;q=0.8,ru;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json',
            'Origin' => 'https://webforms.garantibbva.com.tr',
            'Pragma' => 'no-cache',
            'Referer' => 'https://webforms.garantibbva.com.tr/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            'channel' => 'Internet',
            'client-id' => 'DslahJXaDW59ibNZppCm',
            'client-session-id' => 'c8fe-5015-c4f4-470e-8d91',
            'client-type' => 'ArkClient',
            'dialect' => 'TR',
            'guid' => str_replace('-', '', Uuid::uuid4()),
            'ip' => '127.0.0.1',
            'sec-ch-ua' => '"Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
            'state' => '',
            'tenant-app-id' => '',
            'tenant-company-id' => 'GAR',
            'tenant-geolocation' => 'TUR',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        foreach (json_decode($res->getBody()->getContents(), true) as $item) {
            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => Service::replace(self::REPLACES, $item['currCode']),
                'buy' => $item['exchBuyRate'],
                'sell' => $item['exchSellRate'],
                'time' => $item['currDate'].' '.$item['currTime'],
                'description' => $item['currDesc'].' ('.$item['currFlagCode'].')',
            ];
        }

        return $this->items;
    }
}
