<?php
// Development settings
return [
  \App\Services\Database::class => DI\autowire()->constructorParameter(
    "settings",
    [
      "config" => DI\get("db.config"),
      "user" => DI\get("db.user"),
      "password" => DI\get("db.password"),
    ]
  )
];
