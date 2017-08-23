<?php
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
require 'classes/UserService.php';
require 'vendor/autoload.php';

// -------------------------------------------------
// CREATE APPLICATION
// -------------------------------------------------

$app = new \Slim\App;

// -------------------------------------------------
// ROUTES
// -------------------------------------------------

// Find quickest route between Github repo's contributed by given users.
$app->get('/users', function (ServerRequestInterface $request, ResponseInterface $response) {
    /** @var UserService $userService */
    $userService = new UserService();
    $result = $userService->handleRequest($request->getQueryParams());
    $response->withHeader('Content-type', 'application/json');
    $response->withStatus($result['code']);
    unset($result['code']);
    echo json_encode($result);
    return $response;
})->setName('users');

$app->run();