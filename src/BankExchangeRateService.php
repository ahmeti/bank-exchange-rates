<?php

namespace Ahmeti\BankExchangeRates;

class BankExchangeRateService
{
    protected $rates = [];

    protected function merge(string $key, array $items)
    {
        foreach ($items as $item) {
            if (!array_key_exists($item['curr_code'], $this->rates)) {
                $this->rates[$item['curr_code']] = [];
            }

            $this->rates[$item['curr_code']] = [$key => $item];
        }
    }

    public function get(): array
    {
        $this->merge(Garanti::KEY, (new Garanti)->get());

        return $this->rates;
    }
}
