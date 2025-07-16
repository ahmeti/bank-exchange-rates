<?php

namespace Ahmeti\BankExchangeRates;

use DateTime;
use GuzzleHttp\Client;

class AkBank
{
    const KEY = 'akbank';

    const NAME = 'Akbank';

    const BASE_URL = 'https://www.akbank.com';

    const DATA_URL = 'https://www.akbank.com/_layouts/15/Akbank/CalcTools/Ajax.aspx/GetDovizKurlari';

    protected $items = [];

    public function get(): array
    {
        $client = new Client;
        $headers = [
            'Accept' => '*/*',
            'Accept-Language' => 'tr',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json; charset=utf-8',
            'Origin' => 'https://www.akbank.com',
            'Pragma' => 'no-cache',
            'Referer' => 'https://www.akbank.com/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not)A;Brand";v="8", "Chromium";v="138", "Google Chrome";v="138"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ];

        $res = $client->post(self::DATA_URL, [
            'headers' => $headers,
            'body' => '{"kurTuru":"8"}',
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        foreach ($data['d']['Data']['DovizKurlari'] as $item) {
            if ($item['KurTuru'] !== '8') {
                continue;
            }

            $this->items[] = [
                'key' => self::KEY,
                'name' => self::NAME,
                'symbol' => $item['AlfaKod'].'/TRY',
                'buy' => $item['DovizAlis'],
                'sell' => $item['DovizSatis'],
                'time' => DateTime::createFromFormat('d.m.Y H:i:s', $item['KurGuncellemeZamani'])->format('Y-m-d H:i:s'),
                'description' => $item['DovizAdi'],
            ];

        }

        return $this->items;
    }
}
