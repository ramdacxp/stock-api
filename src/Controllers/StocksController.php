<?php

namespace App\Controllers;

use App\Services\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StocksController
{
  private Database $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
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
  public function get(Request $request, Response $response, array $args): Response
  {
    $isin = $args["isin"] ?? "";
    if (empty($isin)) {
      return $this->errorResponse($response, "Parameter 'isin' not given", 501);
    } else {
      // $data = $this->dsin->addHistory($id, 123);
      $data = $this->db->getLatestHistory($isin);
      return $this->jsonResponse($response, $data);
    }
  }

  private function errorResponse(Response $response, string $message, int $statusCode = 501): Response
  {
    $error = json_encode(array('error' => $message, 'code' => $statusCode));
    $response->getBody()->write($error);

    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($statusCode);
  }

  private function jsonResponse(Response $response, array $data): Response
  {
    $response->getBody()->write(json_encode($data));
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(200);
  }
}
