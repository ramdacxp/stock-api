<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// configure logging
const DISPLAY_ERROR_DETAILS = false;
const LOG_ERRORS = true;
const LOG_ERROR_DETAILS = true;

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
  $response->getBody()->write("Hello world!");
  return $response;
});

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(DISPLAY_ERROR_DETAILS, LOG_ERRORS, LOG_ERROR_DETAILS);

$app->run();
