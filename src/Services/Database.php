<?php

namespace App\Services;

use \PDO;

class Database
{
  protected array $settings;
  protected PDO $pdo;

  // settings as defines in Common.php (array with: config, user, password)
  public function __construct(array $settings)
  {
    $this->settings = $settings;
  }

  public function connect()
  {
    $this->pdo = new PDO(
      $this->settings["config"],
      $this->settings["user"],
      $this->settings["password"],
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]
    );
  }

  public function ensureConnection()
  {
    if (! isset($this->pdo)) {
      $this->connect();
    }
  }

  public function getAllStocks(): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->query("SELECT * FROM stocks");
    $stocks = $stmt->fetchAll();
    return $stocks;
  }

  public function getLatestHistory(string $isin, int $limit = 1): array
  {
    // PDO does not support placeholder for LIMIT; validate it here!
    $limit = max(1, $limit);

    $this->ensureConnection();

    $stmt = $this->pdo->prepare("
      SELECT
        stocks.isin,
        history.ts,
        history.price,
        DATE_FORMAT(history.ts, '%Y-%m-%dT%H:%i:%s') AS time
       FROM history
        INNER JOIN stocks ON history.ref = stocks.id
        WHERE stocks.isin = :isin
        ORDER BY history.ts DESC
        LIMIT $limit
    ");
    $stmt->execute([":isin" => $isin]);

    $latestHistory = $limit == 1 ? $stmt->fetch() : $stmt->fetchAll();
    return $latestHistory ?: [];
  }

  public function getStockId(string $isin): int
  {
    $this->ensureConnection();

    $stmt = $this->pdo->prepare("SELECT id FROM stocks WHERE stocks.isin = :isin");
    $stmt->execute([":isin" => $isin]);

    $data = $stmt->fetch();
    return $data["id"] ?? -1;
  }

  public function addHistory(int $stockId, float $price): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->prepare("INSERT INTO history (ref, price) VALUES (:ref, :price)");
    $stmt->execute([
      ":ref" => $stockId,
      ":price" => $price
    ]);

    $stmt = $this->pdo->prepare("SELECT * FROM history WHERE id = :id");
    $stmt->execute([":id" => $this->pdo->lastInsertId()]);
    $data = $stmt->fetch();

    return $data;
  }

  public function addStocks(string $isin, string $name): int
  {
    $this->ensureConnection();

    $stmt = $this->pdo->prepare("INSERT INTO stocks (isin, name) VALUES (:isin, :name)");
    $stmt->execute([
      ":isin" => $isin,
      ":name" => $name
    ]);

    return $this->pdo->lastInsertId();
  }

}
