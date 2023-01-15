<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class Garanti
{
    const KEY = 'garanti';
    const NAME = 'Garanti';
    const BASE_URL = 'https://www.garantibbva.com.tr';
    const DATA_URL = 'https://customers.garantibbva.com.tr/internet/digitalpublic/currency-convertor-public/v1/currency-convertor/currency-list-detail';
    const REPLACES = [
        '/TL' => '/TRY',
        'TL/' => 'TRY/',
        '/ALT' => '/XAU',
        'ALT/' => 'XAU/',
    ];

    protected $items = [];

    public function get(): array
    {
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'tr-TR,tr;q=0.9',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'channel' => 'Public',
            'client-type' => 'ArkClient',
            'dialect' => 'TR',
            'guid' => str_replace('-', '', Uuid::uuid4()),
            'ip' => '127.0.0.1',
            'tenant-company-id' => 'GAR',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        foreach (json_decode($res->getBody()->getContents(), true) as $item) {
            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => Service::replace(self::REPLACES, $item['currCode']),
                'buy' => $item['exchBuyRate'],
                'sell' => $item['exchSellRate'],
                'time' => $item['currDate'] . ' ' . $item['currTime'],
                'description' => $item['currDesc'] . ' (' . $item['currFlagCode'] . ')',
            ];
        }

        return $this->items;
    }
}
