<?php

namespace App\Services;

class Downloader
{
  // private array $settings;

  // public function __construct(array $settings)
  // {
  //   $this->settings = $settings;
  // }

  public function downloadStock(string $isin): array
  {
    $baseUrl = "https://component-api.wertpapiere.ing.de/api/v1/components/instrumentheader/";
    $url = $baseUrl . urlencode($isin);
    $json = file_get_contents($url);

    if ($json === false) {
      throw new \Exception("Failed to fetch data from URL: $url");
    }

    // Decode JSON into an associative array
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new \Exception("Failed to decode JSON: " . json_last_error_msg());
    }

    // {
    //   "id": 951692,
    //   "name": "Microsoft",
    //   "price": 321.3,
    //   "close": 323.15,
    //   "bid": 321.15,
    //   "bidDate": "2025-04-22T21:59:45+02:00",
    //   "ask": 321.3,
    //   "askDate": "2025-04-22T21:59:45+02:00",
    //   "changePercent": -0.572489555933777,
    //   "changeAbsolute": -1.85,
    //   "instrumentTypeDisplayName": "Aktie",
    //   "wkn": "870747",
    //   "isin": "US5949181045",
    //   "internalIsin": "US5949181045",
    //   "stockMarket": "Direkthandel",
    //   "priceChangeDate": "2025-04-22T21:59:47+02:00",
    //   "currency": "EUR",
    //   "currencySign": "EUR",
    //   "pushSymbol": "X000ADB0200951692",
    // }
    return $data;
  }


}
