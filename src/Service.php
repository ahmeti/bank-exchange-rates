<?php

namespace Ahmeti\BankExchangeRates;

use DateTimeZone;

class Service
{
    protected $rates = [];

    protected function merge(array $items): void
    {
        foreach ($items as $item) {
            if (!array_key_exists($item['symbol'], $this->rates)) {
                $this->rates[$item['symbol']] = [];
            }

            $this->rates[$item['symbol']][] = $item;
        }
    }

    public static function timeZone(): DateTimeZone
    {
        return new DateTimeZone('Europe/Istanbul');
    }

    public static function toFloat(string $text): float
    {
        return (float)str_replace(['.', ','], ['', '.'], $text);
    }

    public static function replace(array $replaces, $symbol): string
    {
        return str_replace(array_keys($replaces), array_values($replaces), $symbol);
    }

    public function get(): array
    {
        $this->merge((new Garanti)->get());
        $this->merge((new YapiKredi)->get());
        $this->merge((new HalkBank)->get());
        $this->merge((new EnPara)->get());
        $this->merge((new AkBank)->get());
        $this->merge((new IsBankasi)->get());
        $this->merge((new KuveytTurk)->get());
        $this->merge((new Ziraat)->get());

        return $this->rates;
    }
}
