<?php

use \App\Services\Database;
use \App\Services\StockDataDatabase;

// Development settings
return [
  Database::class => DI\autowire()->constructorParameter(
    "settings",
    [
      "config" => DI\get("db.config"),
      "user" => DI\get("db.user"),
      "password" => DI\get("db.password"),
    ]
  ),
  StockDataDatabase::class => DI\autowire()->constructorParameter(
    "settings",
    [
      "config" => DI\get("db.config"),
      "user" => DI\get("db.user"),
      "password" => DI\get("db.password"),
    ]
  )
];
