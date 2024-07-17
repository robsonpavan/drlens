<?php 

//Iniciando o uso de sessão
if (!isset($_SESSION)) {
    session_start();
}

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\App;
use rapinformatica\Page;
use rapinformatica\PageAdmin;
use rapinformatica\Model\User;
use rapinformatica\Model\Category;

require_once("vendor/autoload.php");


$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require_once("functions.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");


//$app->config('debug', true);

$app->run();

 ?>