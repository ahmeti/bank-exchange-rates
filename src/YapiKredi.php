<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class YapiKredi
{
    const KEY = 'yapikredi';
    const BASE_URL = 'https://www.yapikredi.com.tr';
    const DATA_URL = 'https://www.yapikredi.com.tr/yatirimci-kosesi/doviz-bilgileri';

    protected $items = [];

    protected function textToFloat($text)
    {
        return (float)str_replace(',', '.', $text);
    }

    public function get(): array
    {
        $client = new Client();
        $headers = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
        ];

        $res = $client->get(self::DATA_URL, [
            'headers' => $headers
        ]);

        $crawler = new Crawler($res->getBody()->getContents());

        $lastUpdate = $crawler->filter('p.dipnote')->html();
        $lastUpdate = str_replace('Güncelleme : ', '', $lastUpdate);
        $lastUpdate = DateTime::createFromFormat('d.m.Y H:i:s', $lastUpdate);
        $lastUpdate = $lastUpdate->format('Y-m-d H:i:s');

        $tbody = $crawler->filter('tbody#currencyResultContent>tr');
        $tbody->each(function ($tr, $i) use ($lastUpdate) {
            $firstTd = $tr->filterXPath('//td');
            $this->items[] = [
                'curr_code' => $firstTd->first()->text() . '/TRY',
                'buy' => $this->textToFloat($firstTd->attr('data-previousdaybuyingprice')),
                'sell' => $this->textToFloat($firstTd->attr('data-previousdaysellingprice')),
                'time' => $lastUpdate,
                'description' => null,
            ];
        });

        return $this->items;
    }
}
