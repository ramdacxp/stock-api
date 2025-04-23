<?php

namespace App\Controllers;

use App\Services\Database;
use App\Services\Downloader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StocksController
{
  private Database $db;
  private Downloader $downloader;

  public function __construct(Database $db, Downloader $downloader)
  {
    $this->db = $db;
    $this->downloader = $downloader;
    // print_r($db->get());
  }

  // public function __invoke(Request $request, Response $response, array $args): Response

  // GET /stocks
  public function list(Request $request, Response $response, array $args): Response
  {
    $stocks = $this->db->getAllStocks();

    // return as: isin => name
    $data = [];
    foreach ($stocks as $record) {
      $data[$record["isin"]] = $record["name"];
    }
    return $this->jsonResponse($response, $data);
  }

  // GET /stocks/{id}
  // GET /stocks/{id}/{limit}
  public function get(Request $request, Response $response, array $args): Response
  {
    $isin = $args["isin"] ?? "";
    $limit = max(1, $args["limit"] ?? 1);

    if (empty($isin)) {
      return $this->errorResponse($response, "Parameter 'isin' not given", 501);
    } else {
      // $data = $this->dsin->addHistory($id, 123);
      $data = $this->db->getLatestHistory($isin, $limit);
      return $this->jsonResponse($response, $data);
    }
  }

  public function query(Request $request, Response $response, array $args): Response
  {
    $isin = $args["isin"] ?? "";
    if (empty($isin)) {
      return $this->errorResponse($response, "Parameter 'isin' not given", 501);
    } else {

      // dwonload the data from the Diba API
      $data = $this->downloader->downloadStock($isin);

      // create new stocks entry?
      $stockId = $this->db->getStockId($isin);
      if ($stockId == -1) {
        $stockId = $this->db->addStocks($data["isin"], $data["name"]);
      }

      if ($stockId == -1) {
        return $this->errorResponse($response, "Could not download data for given isin", 501);
      } else {
        $newRecord = $this->db->addHistory($stockId, $data["price"]);
        return $this->jsonResponse($response, $newRecord);
      }
    }
  }

  private function errorResponse(Response $response, string $message, int $statusCode = 501): Response
  {
    $error = json_encode(array("error" => $message, "code" => $statusCode));
    $response->getBody()->write($error);

    return $response
      ->withHeader("Content-Type", "application/json")
      ->withStatus($statusCode);
  }

  private function jsonResponse(Response $response, array $data): Response
  {
    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return $response
      ->withHeader("Content-Type", "application/json")
      ->withStatus(200);
  }
}
