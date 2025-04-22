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

  public function get(string $key = "")
  {
    return (empty($key)) ? $this->settings : $this->settings[$key];
  }

  public function query(): array
  {
    $this->ensureConnection();

    $stmt = $this->pdo->query("SELECT * FROM stocks");
    $stocks = $stmt->fetchAll();
    return $stocks;
  }
}
