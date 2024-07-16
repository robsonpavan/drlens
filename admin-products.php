<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\App;
use \rapinformatica\PageAdmin;
use \rapinformatica\Model\User;
use \rapinformatica\Model\Product;

//Rota para acessar página de produtos
$app->get("/admin/products", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $products = Product::listAll();

    $page = New PageAdmin();

    $page->setTpl("products", array(
        "products" => $products
    ));
    return $response;
});

//Rota para página de criação de produtos
$app->get("/admin/products/create", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $page = new PageAdmin();

    //Definindo qual página deverá ser desenhada 
    $page->setTpl("products-create");
    return $response;
});

//Rota pra salvar novos produtos
$app->post("/admin/products/create", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $products = new Product();

    //Capturando os dados do formulário e inserindo no objeto products
    $products->setData($_POST);

    $products->save();
    
    $products->setPhoto($_FILES["file"]);

    //Encaminhando para página que lista os produtos
    header("Location: /admin/products");
    exit;
    return $response;
});

//Rota para editar o produto
$app->get("/admin/products/{idproduct}", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $idproduct = $args['idproduct'];

    $product = new Product();

    $product->get((int) $idproduct);

    $page = New PageAdmin();

    //Passado as informações do produto carregado no objeto na linha 65 ($product->get((int)$idproduct);)
    $page->setTpl("products-update", array(
        "product" => $product->getValues()
    ));
    return $response;
});

//Rota para salvar as alterações no produto
$app->post("/admin/products/{idproduct}", function(Request $request, Response $response, $args) {
    User::verifyLogin();
    $idproduct = $args['idproduct'];
    $product = new Product();
    $product->get((int) $idproduct);
    $product->setData($_POST);
    $product->save();
    $product->setPhoto($_FILES["file"]);
    header('Location: /admin/products');
    exit;
    return $response;
});

//Rota para excluir os produtos]
$app->get("/admin/products/{idproduct}/delete", function(Request $request, Response $response, $args) {
    User::verifyLogin();
    $idproduct = $args['idproduct'];
    $product = new Product();
    $product->get((int) $idproduct);
    $product->delete();
    header('Location: /admin/products');
    exit;
    return $response;
});

