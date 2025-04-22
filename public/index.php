<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

use App\Controllers\StocksController;

require __DIR__ . "/../vendor/autoload.php";

// configure logging
const DISPLAY_ERROR_DETAILS = true;
const LOG_ERRORS = true;
const LOG_ERROR_DETAILS = true;

// configure DI
$builder = new ContainerBuilder();
$settingsFolder = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), "src", "Settings"]);
$prodSettings = $settingsFolder . DIRECTORY_SEPARATOR . "Production.php";
$devSettings = $settingsFolder . DIRECTORY_SEPARATOR . "Development.php";
$builder->addDefinitions(file_exists($prodSettings) ? $prodSettings : $devSettings);
$builder->addDefinitions($settingsFolder . DIRECTORY_SEPARATOR . "Common.php");

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

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(DISPLAY_ERROR_DETAILS, LOG_ERRORS, LOG_ERROR_DETAILS);

$app->run();
