<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Ziraat
{
    const KEY = 'ziraat';

    const NAME = 'Ziraat';

    const BASE_URL = 'https://www.ziraatbank.com.tr';

    const DATA_URL = 'https://www.ziraatbank.com.tr/tr/_layouts/15/Ziraat/HomePage/Ajax.aspx/GetZiraatVerileri';

    const REPLACES = [
        'AMERIKAN DOLARI' => 'USD/TRY',
        'EURO' => 'EUR/TRY',
        'A02 ALTIN (1000/1000)' => 'XAU/TRY',
    ];

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => 'text/plain, */*; q=0.01',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Content-Type' => 'core/json',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.ziraatbank.com.tr/tr',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            'X-Requested-With' => 'JQuery PageEvents',
            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers,
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        $crawler = new Crawler($data['d']['Data']['Html']);

        $lastUpdate = $crawler->filter('div > span')->last()->html();
        $lastUpdate = (DateTime::createFromFormat('d.m.Y - H:i:s', $lastUpdate, Service::timeZone()))->format('Y-m-d H:i:s');

        $crawler->filter('ul > li')->each(function ($item, $i) use ($lastUpdate) {

            $name = $item->filterXPath('//h2')->text();
            $buy = $item->filterXPath('//div/div/span')->first()->text();
            $sell = $item->filterXPath('//div/div/span')->last()->text();

            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => Service::replace(self::REPLACES, $name),
                'buy' => Service::toFloat($buy),
                'sell' => Service::toFloat($sell),
                'time' => $lastUpdate,
                'description' => null,
            ];

        });

        return $this->items;
    }
}
