<?php

namespace App\Services;

class Downloader
{
  private array $settings;

  public function __construct(array $settings)
  {
    $this->settings = $settings;
  }

}
