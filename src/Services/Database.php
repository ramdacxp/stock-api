<?php

namespace App\Services;

class Database
{
  private array $settings;
  private \PDO $pdo;

  public function __construct(array $settings)
  {
    $this->settings = $settings;
  }

  public function connect()
  {
    $this->pdo = new \PDO(
      $this->settings["db.config"],
      $this->settings["db.user"],
      $this->settings["db.password"]
    );
  }

  public function get(string $key = "")
  {
    return (empty($key)) ? $this->settings : $this->settings[$key];
  }
}
