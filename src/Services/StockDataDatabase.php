<?php

namespace App\Services;

class StockDataDatabase extends Database
{
  public function getAllStocks(): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->query("SELECT DISTINCT `isin`, `name` FROM `stockdata`");
    $stocks = $stmt->fetchAll();
    return $stocks;
  }

  public function getStockDetails(string $isin, int $limit = 1): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->prepare("SELECT
        `name`,
        `isin`,
        `price`,
        `currency`,
        DATE_FORMAT(`priceChange`, '%Y-%m-%dT%H:%i:%s') AS ts
      FROM `stockdata`
      WHERE isin = :isin
      ORDER BY priceChange DESC
      LIMIT " . $limit);

    $stmt->execute([":isin" => $isin]);
    $data = $stmt->fetchAll();

    return $data;
  }

  public function getDailyStockDetails(string $isin, int $limit = 1): array
  {
    $this->ensureConnection();

    // TODO: Add latest (!) price of the day as well
    $stmt = $this->pdo->prepare("SELECT
        DATE(`priceChange`) AS `day`,
        `isin`, `name`, `price`, `currency`
      FROM `stockdata`
      WHERE
        isin = :isin AND
        id in (SELECT max(id) FROM `stockdata` GROUP BY DATE(`priceChange`))
      ORDER BY id DESC
      LIMIT " . $limit);

    $stmt->execute([":isin" => $isin]);
    $data = $stmt->fetchAll();

    return $data;
  }

  public function addStock(string $name, string $isin, float $price, string $currency, string $priceChange): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->prepare("INSERT INTO `stockdata`
      (`name`, `isin`, `price`, `currency`, `priceChange`) VALUES (:name, :isin, :price, :currency, :change)");
    $stmt->execute([
      ":name" => $name,
      ":isin" => $isin,
      ":price" => $price,
      ":currency" => $currency,
      ":change" => $priceChange
    ]);

    $stmt = $this->pdo->prepare("SELECT * FROM `stockdata` WHERE id = :id");
    $stmt->execute([":id" => $this->pdo->lastInsertId()]);
    $newRecord = $stmt->fetch();

    return $newRecord;
  }
}
