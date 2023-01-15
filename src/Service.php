<?php

namespace Ahmeti\BankExchangeRates;

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

    public function toFloat($text): float
    {
        return (float)str_replace(['.', ','], ['', '.'], $text);
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

        return $this->rates;
    }
}
