<?php

namespace App\Services;

class Database
{
  private array $settings;

  public function __construct(array $settings)
  {
    $this->settings = $settings;
  }

  public function get(string $key = "")
  {
      return (empty($key)) ? $this->settings : $this->settings[$key];
  }
}
