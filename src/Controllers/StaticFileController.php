<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StaticFileController
{
  public function __construct() {}

  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $filename = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), "Html", $args["file"]]);
    if ( file_exists($filename)) {
      $content = file_get_contents($filename);
      $response->getBody()->write($content);
      return $response->withStatus(200);
    } else {
      $response->getBody()->write($args["file"] . " not found!");
      return $response->withStatus(404);
    }
  }
}
