<?php

namespace App\Controllers;

use App\Services\Downloader;
use App\Services\StockDataDatabase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StockDataController extends JsonController
{
  private StockDataDatabase $db;
  private Downloader $downloader;

  public function __construct(StockDataDatabase $db, Downloader $downloader)
  {
    $this->db = $db;
    $this->downloader = $downloader;
  }

  // GET /history
  public function getAllStocks(Request $request, Response $response, array $args): Response
  {
    $stocks = $this->db->getAllStocks();

    // return as: isin => name
    $data = [];
    foreach ($stocks as $record) {
      $data[$record["isin"]] = $record["name"];
    }

    return $this->generateJsonResponse($response, $data);
  }

  // GET /history/{isin}
  // ARGUMENT: limit
  public function getStockDetails(Request $request, Response $response, array $args): Response
  {
    $isin = $args["isin"] ?? "";
    if (empty($isin)) return $this->generateArgumentErrorResponse($response, "isin");

    $limit = max(1, (int) $args["limit"] ?? 1);
    $data = $this->db->getStockDetails($isin, $limit);
    return $this->generateJsonResponse($response, $data);
  }

  // GET /query/{isin}
  public function addStock(Request $request, Response $response, array $args): Response
  {
    $isin = $args["isin"] ?? "";
    if (empty($isin)) return $this->generateArgumentErrorResponse($response, "isin");

    // download from the Diba API
    $data = $this->downloader->downloadStock($isin);

    $newRecord = $this->db->addStock(
      $data["name"],
      $data["isin"],
      $data["price"],
      $data["currency"],
      substr($data["priceChangeDate"], 0, 19) // cut off all after seconds from: 2025-04-23T11:54:05+02:00
    );

    return $this->generateJsonResponse($response, $newRecord);
  }
}
