# Banka Döviz Kurları - Api
Bu paket ile aşağıda belirtilen bankaların, herkese açık (public) kur bilgilerine ulaşabilirsiniz.

## Composer ile Yükleme
```
composer require ahmeti/bank-exchange-rates
```

```php
require __DIR__ . '/vendor/autoload.php';

$service = new \Ahmeti\BankExchangeRates\Service();

print_r($service->get());

if ($service->hasError()) {
    print_r($service->getErrors());
}
```

## Banka Listesi
- Garanti
- Yapı Kredi
- Halkbank
- Enpara
- Akbank
- İş Bankası
- Kuveyt Türk
- Ziraat
- Cepte TEB

## Örnek Veriler

```php
[
  "USD/TRY" => [
    [
      "name" => "Garanti",
      "symbol" => "USD/TRY"
      "buy" => 18.463
      "sell" => 19.063
      "time" => "2023-01-06 00:00:37"
      "description" => "Amerikan Doları (us/tr)"
    ],
    [
      "name" => "Yapı Kredi",
      "symbol" => "USD/TRY"
      "buy" => 18.70675
      "sell" => 18.94574
      "time" => "2023-01-06 00:16:08"
      "description" => null
    ]
  ]
  
  "EUR/TRY" => [
    [
      "name" => "Garanti",
      "symbol" => "EUR/TRY"
      "buy" => 19.427
      "sell" => 20.059
      "time" => "2023-01-06 00:00:37"
      "description" => "Avrupa Para Birimi (eu/tr)"
    ],
    [
      "name" => "Yapı Kredi",
      "symbol" => "EUR/TRY"
      "buy" => 19.70949
      "sell" => 19.96007
      "time" => "2023-01-06 00:16:08"
      "description" => null
    ]
  ]
]
...
```
