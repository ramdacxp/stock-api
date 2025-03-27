<?php
// -----------------------------------------------------------------------------
// Template for production settings
// -> Rename to "Production.php" and adapt content to activate it.
// -----------------------------------------------------------------------------
return [
  // Prod/Dev install
  "isProduction" => true,

  // PDO database settings
  "db.config" => "mysql:host=myHost;dbname=stocks",
  "db.user" => "myUser",
  "db.password" => ""
];
