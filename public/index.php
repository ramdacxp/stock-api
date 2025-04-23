<?php

use App\Controllers\StaticFileController;
use App\Controllers\StockDataController;
use Slim\Factory\AppFactory;

use App\Controllers\StocksController;
use App\DI\ExtendedContainerBuilder;

require __DIR__ . "/../vendor/autoload.php";

// Configure DI
$builder = new ExtendedContainerBuilder();
$builder->addDefinitionFromFileWithFallback("Production.php", "Development.php");
$builder->addDefinitionFromFile("Common.php");

// Create app instance
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add all the routes
$app->get("/", StaticFileController::class)->setArgument("file", "Start.html");

// $app->get("/stocks", [StocksController::class, "list"]);
// $app->get("/stocks/{isin}", [StocksController::class, "get"]);
// $app->get("/stocks/{isin}/{limit}", [StocksController::class, "get"]);
// $app->get("/query/{isin}", [StocksController::class, "query"]);

$app->get("/query/{isin}", [StockDataController::class, "addStock"]);

$app->get("/history", [StockDataController::class, "getAllStocks"]);
$app->get("/history/{isin}", [StockDataController::class, "getStockDetails"])->setArgument("limit", 1);
$app->get("/history/{isin}/{limit}", [StockDataController::class, "getStockDetails"]);
$app->get("/daily/{isin}", [StockDataController::class, "getDailyStockDetails"])->setArgument("limit", 1);
$app->get("/daily/{isin}/{limit}", [StockDataController::class, "getDailyStockDetails"]);

// Add routing middleware AFTER adding the router
$app->addRoutingMiddleware();

// Report Exception as JSON response
$app->add((new Middlewares\JsonExceptionHandler())
  ->includeTrace(false)
  ->jsonOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Alternative end user error logging
// const DISPLAY_ERROR_DETAILS = false;
// const LOG_ERRORS = true;
// const LOG_ERROR_DETAILS = true;
// $app->addErrorMiddleware(DISPLAY_ERROR_DETAILS, LOG_ERRORS, LOG_ERROR_DETAILS);

// Done. Run it!
$app->run();
