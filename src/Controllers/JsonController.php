<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class JsonController
{
  protected function generateErrorResponse(Response $response, string $message, int $statusCode = 501): Response
  {
    $error = json_encode(array("error" => $message, "code" => $statusCode));
    $response->getBody()->write($error);

    return $response
      ->withHeader("Content-Type", "application/json")
      ->withStatus($statusCode);
  }

  // Generates a 400 Bad Request response error message
  protected function generateArgumentErrorResponse(Response $response, string $argumentName, int $statusCode = 400): Response
  {
    return $this->generateErrorResponse($response, "Argument '" . $argumentName . "' was not provided or is invalid", $statusCode);
  }

  protected function generateJsonResponse(Response $response, array $data): Response
  {
    $response->getBody()->write(
      json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
    return $response
      ->withHeader("Content-Type", "application/json")
      ->withStatus(200);
  }

}
