<?php
// Development settings
return [
  \App\Services\Database::class => DI\autowire()->constructorParameter(
    "settings",
    [
      // "host" => DI\get('db.host'),
      "user" => DI\get('db.user'),
      "password" => DI\get('db.password')
    ]
  )
];
