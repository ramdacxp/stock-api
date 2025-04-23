<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use App\Controllers\StocksController;
use App\DI\ExtendedContainerBuilder;

require __DIR__ . "/../vendor/autoload.php";

// configure logging
const DISPLAY_ERROR_DETAILS = true;
const LOG_ERRORS = true;
const LOG_ERROR_DETAILS = true;

// configure DI
$builder = new ExtendedContainerBuilder();
$builder->addDefinitionFromFileWithFallback("Production.php", "Development.php");
$builder->addDefinitionFromFile("Common.php");

// create app instance
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get("/", function (Request $request, Response $response, $args) {
  $html = file_get_contents(implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), "src", "Html", "Start.html"]));
  $response->getBody()->write($html);
  return $response;
});

$app->get("/stocks", [StocksController::class, "list"]);
$app->get("/stocks/{isin}", [StocksController::class, "get"]);
$app->get("/stocks/{isin}/{limit}", [StocksController::class, "get"]);
$app->get("/query/{isin}", [StocksController::class, "query"]);

$app->addRoutingMiddleware();
$app->add((new Middlewares\JsonExceptionHandler())->includeTrace(false)->jsonOptions(JSON_PRETTY_PRINT));
// $errorMiddleware = $app->addErrorMiddleware(DISPLAY_ERROR_DETAILS, LOG_ERRORS, LOG_ERROR_DETAILS);

$app->run();
