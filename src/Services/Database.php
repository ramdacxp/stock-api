<?php

namespace App\Services;

class Database
{
  private array $settings;
  private \PDO $pdo;

  // settings as defines in Common.php (array with: config, user, password)
  public function __construct(array $settings)
  {
    $this->settings = $settings;
  }

  // public function get(string $key = "")
  // {
  //   return (empty($key)) ? $this->settings : $this->settings[$key];
  // }

  public function connect()
  {
    $this->pdo = new \PDO(
      $this->settings["config"],
      $this->settings["user"],
      $this->settings["password"],
      [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
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

  public function getLatestHistory(string $isin): array
  {
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
      LIMIT 1
    ");
    $stmt->execute([':isin' => $isin]);

    $latestHistory = $stmt->fetch();
    return $latestHistory ?: [];
  }

  public function addHistory(int $stockId, int $price): array
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
}
