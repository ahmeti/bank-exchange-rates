<?php

namespace Ahmeti\BankExchangeRates;

use DateTimeZone;
use Exception;

class Service
{
    protected array $rates = [];

    protected array $exceptions = [];

    protected function merge(array $items): void
    {
        foreach ($items as $item) {
            if (! array_key_exists($item['symbol'], $this->rates)) {
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
        return (float) str_replace(['.', ','], ['', '.'], $text);
    }

    public static function replace(array $replaces, $symbol): string
    {
        return str_replace(array_keys($replaces), array_values($replaces), $symbol);
    }

    public function hasError(): bool
    {
        return ! empty($this->exceptions);
    }

    public function getErrors(): array
    {
        return $this->exceptions;
    }

    public function get(): array
    {
        try {
            $this->merge((new Garanti)->get());
        } catch (Exception $exception) {
            $this->exceptions[Garanti::KEY] = $exception;
        }

        try {
            $this->merge((new YapiKredi)->get());
        } catch (Exception $exception) {
            $this->exceptions[YapiKredi::KEY] = $exception;
        }

        try {
            $this->merge((new HalkBank)->get());
        } catch (Exception $exception) {
            $this->exceptions[HalkBank::KEY] = $exception;
        }

        try {
            $this->merge((new EnPara)->get());
        } catch (Exception $exception) {
            $this->exceptions[EnPara::KEY] = $exception;
        }

        try {
            $this->merge((new AkBank)->get());
        } catch (Exception $exception) {
            $this->exceptions[AkBank::KEY] = $exception;
        }

        try {
            $this->merge((new IsBankasi)->get());
        } catch (Exception $exception) {
            $this->exceptions[IsBankasi::KEY] = $exception;
        }

        try {
            $this->merge((new KuveytTurk)->get());
        } catch (Exception $exception) {
            $this->exceptions[KuveytTurk::KEY] = $exception;
        }

        try {
            $this->merge((new Ziraat)->get());
        } catch (Exception $exception) {
            $this->exceptions[Ziraat::KEY] = $exception;
        }

        try {
            $this->merge((new CepteTeb)->get());
        } catch (Exception $exception) {
            $this->exceptions[CepteTeb::KEY] = $exception;
        }

        return $this->rates;
    }
}
