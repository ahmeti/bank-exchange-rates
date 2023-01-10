# Banka Döviz Kurları - Api
Bu paket ile belirli bankaların, herkese açık (public) kur bilgilerine ulaşabilirsiniz.

## Composer ile Yükleme
```
composer require ahmeti/bank-exchange-rates
```

```php
require __DIR__ . '/vendor/autoload.php';

try {
    $rates = (new \Ahmeti\BankExchangeRates\BankExchangeRateService)->get();
    print_r($rates);
    
}catch (\Exception $exception){
    print_r($exception);
}
```

## Banka Listesi
- Garanti Bankası
- Yapı Kredi Bankası
- Halk Bankası
- Enpara
- Akbank

## Örnek Veriler

```php
[
  "USD/TRY" => [
    "garanti" => [
      "name" => "Garanti",
      "symbol" => "USD/TRY"
      "buy" => 18.463
      "sell" => 19.063
      "time" => "2023-01-06 00:00:37"
      "description" => "Amerikan Doları (us/tr)"
    ]
    "yapikredi" => [
      "name" => "Yapı Kredi",
      "symbol" => "USD/TRY"
      "buy" => 18.70675
      "sell" => 18.94574
      "time" => "2023-01-06 00:16:08"
      "description" => null
    ]
  ]
  
  "EUR/TRY" => [
    "garanti" => [
      "name" => "Garanti",
      "symbol" => "EUR/TRY"
      "buy" => 19.427
      "sell" => 20.059
      "time" => "2023-01-06 00:00:37"
      "description" => "Avrupa Para Birimi (eu/tr)"
    ]
    "yapikredi" => [
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