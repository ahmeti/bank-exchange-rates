<?php

namespace Ahmeti\BankExchangeRates;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class EnPara
{
    const KEY = 'enpara';
    const BASE_URL = 'https://www.qnbfinansbank.enpara.com';
    const DATA_URL = 'https://www.qnbfinansbank.enpara.com/hesaplar/doviz-ve-altin-kurlari';

    protected $items = [];

    protected function textToFloat($text)
    {
        return (float)str_replace(['.', ','], ['', '.'], $text);
    }

    protected function replace(string $symbol): string
    {
        return str_replace(
            ['USD ($)', 'EUR (€)', 'Altın (gram)', 'EUR/USD Parite'],
            ['USD/TRY', 'EUR/TRY', 'XAU/TRY',      'EUR/USD'],
            $symbol);
    }

    public function get(): array
    {
        $client = new Client();
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
            'sec-ch-ua-platform' => '"macOS"'
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        $crawler = new Crawler($res->getBody()->getContents());

        $crawler->filter('section#content')
            ->filter('.enpara-gold-exchange-rates__table > .enpara-gold-exchange-rates__table-item')
            ->each(function ($row, $i) {
                $currCode = $row->filter('span')->eq(0)->text();
                $buy = $row->filter('span')->eq(1)->text();
                $sell = $row->filter('span')->eq(2)->text();
                $this->items[] = [
                    'name' => 'En Para',
                    'symbol' => $this->replace($currCode),
                    'buy' => $this->textToFloat($buy),
                    'sell' => $this->textToFloat($sell),
                    'time' => date('Y-m-d H:i:s'),
                    'description' => null,
                ];
            });

        return $this->items;
    }
}
