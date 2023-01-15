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
        $this->merge(HalkBank::KEY, (new HalkBank)->get());
        $this->merge(EnPara::KEY, (new EnPara)->get());
        $this->merge(AkBank::KEY, (new AkBank)->get());
        $this->merge(IsBankasi::KEY, (new IsBankasi)->get());

        return $this->rates;
    }
}
