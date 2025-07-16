<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class EnPara
{
    const KEY = 'enpara';

    const NAME = 'En Para';

    const BASE_URL = 'https://www.qnbfinansbank.enpara.com';

    const DATA_URL = 'https://www.qnbfinansbank.enpara.com/hesaplar/doviz-ve-altin-kurlari';

    const REPLACES = [
        'USD ($)' => 'USD/TRY',
        'EUR (€)' => 'EUR/TRY',
        'Altın (gram)' => 'XAU/TRY',
        'EUR/USD Parite' => 'EUR/USD',
    ];

    protected $items = [];

    public function get(): array
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
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        $crawler = new Crawler($res->getBody()->getContents());

        $crawler->filter('section#content')
            ->filter('.enpara-gold-exchange-rates__table > .enpara-gold-exchange-rates__table-item')
            ->each(function ($row, $i) {
                $currCode = $row->filter('span')->eq(0)->text();
                $buy = $row->filter('span')->eq(1)->text();
                $sell = $row->filter('span')->eq(2)->text();
                $this->items[] = [
                    'key' => self::KEY,
                    'name' => self::NAME,
                    'symbol' => Service::replace(self::REPLACES, $currCode),
                    'buy' => Service::toFloat($buy),
                    'sell' => Service::toFloat($sell),
                    'time' => date('Y-m-d H:i:s'),
                    'description' => null,
                ];
            });

        return $this->items;
    }
}
