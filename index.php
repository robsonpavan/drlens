<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use rapinformatica\Page;
require_once("vendor/autoload.php");


$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/', function (Request $request, Response $response, $args) {
    /*$name = $args['name'];
    $response->getBody()->write("Hello, $name");*/
	
	$page = new Page();

	$page->setTpl("index");

    return $response;
});

// Run app
$app->run();

 ?>