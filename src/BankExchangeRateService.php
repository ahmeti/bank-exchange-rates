<?php

namespace Ahmeti\BankExchangeRates;

class BankExchangeRateService
{
    protected $rates = [];

    protected function merge(string $key, array $items)
    {
        foreach ($items as $item) {
            if (!array_key_exists($item['symbol'], $this->rates)) {
                $this->rates[$item['symbol']] = [];
            }

            $this->rates[$item['symbol']][$key] = $item;
        }
    }

    public function get(): array
    {
        $this->merge(Garanti::KEY, (new Garanti)->get());
        $this->merge(YapiKredi::KEY, (new YapiKredi)->get());

        return $this->rates;
    }
}
