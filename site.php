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
use \rapinformatica\Page;
use \rapinformatica\Model\Category;
use rapinformatica\Model\Product;
use rapinformatica\Model\Cart;

//Configuração da rota '/'
$app->get('/', function(Request $request, Response $response, $args) {
    
    //Instanciando objeto para carregar os produtos a partir do banco de dados
    $products = Product::listAll();
    
    //Carregando o Header - executando o construct
    $page = new Page();
    //Carregando o Index -executando setTPL
    $page->setTpl("index", array(
        "products"=> Product::checklist($products)
    ));
    //Ao final do comado carrega o Footer pois o destruct roda automáricamente no final - executando o destruct
    return $response;
});

//Rota para acesso às categorias acessadas via site
$app->get("/categories/{idcategory}", function (Request $request, Response $response, $args){

    $idcategory = $args['idcategory'];
    
    $pag = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    
    $category = new Category();

    //Carregando o objeto selecionado para edição. è feito cast do id para inteiro pois tudo que é carregado via url é convertido para texto
    $category->get((int)$idcategory);  
    
    //Recebendo os produtos e as informaçõs de paginação
    $pagination = $category->getProductsPage($pag);
    
    //Array criado para enviar o link de navegação da paginação e o número da página a ser acessado
    $pages = [];
    //Populando array
    for ($i = 1; $i <= $pagination['pages']; $i++) {
        array_push($pages, [
            'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
            'page'=>$i
        ]);
    }
    
    
    $page = new Page();
    
    //Carregando a página da categoria, e passando as informações referentes a categoria selecionada
    $page->setTpl("category", [
        'category'=>$category->getValues(),
        'products'=> $pagination['data'],
        'pages'=>$pages
    ]);
       
    return $response;
});

//Rota para acessar os detalhes do produto
$app->get("/products/{desurl}", function(Request $request, Response $response, $args){
    
    $desurl = $args['desurl'];

    $product = new Product();
    
    $product->getFromURL($desurl);
    
    $page = new Page();
    
    //Carregando a página da categoria, e passando as informações referentes a categoria selecionada
    $page->setTpl("product-detail", [
        'product'=>$product->getValues(),
        'categories'=> $product->getCategories()
    ]);
        
    return $response;
});

//Rota para acessar o carrinho
$app->get("/cart", function (Request $request, Response $response, $args){
    
    $cart = Cart::getFromSession();
    
    $page = new Page();
    
    //Carregando a página da categoria, e passando as informações referentes a categoria selecionada
    $page->setTpl("cart", [
        'cart'=> $cart->getValues(),
        'products'=> $cart->getProducts(),
        'error'=>Cart::getMsgError()
    ]);
    return $response;
});

$app->get("/cart/{idproduct}/add", function (Request $request, Response $response, $args){
    
    $product = new Product();

    $idproduct = $args['idproduct'];
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $qtd = (isset($_GET['qtd']))? (int)$_GET['qtd'] : 1;

    for ($i = 0; $i < $qtd; $i++){
        $cart->addProduct($product);
    }
    header("Location: /cart");
    exit;
    return $response;
});

$app->get("/cart/{idproduct}/minus", function (Request $request, Response $response, $args){
    
    $product = new Product();

    $idproduct = $args['idproduct'];
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product);
    
    header("Location: /cart");
    exit;
    return $response;
});

$app->get("/cart/{idproduct}/remove", function (Request $request, Response $response, $args){
    
    $product = new Product();

    $idproduct = $args['idproduct'];
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product, true);
    
    header("Location: /cart");
    exit;
    return $response;
});

$app->post("/cart/freight", function(Request $request, Response $response, $args){

    $cart = Cart::getFromSession();

    $cart->setFreight($_POST['zipcode']);

    header("Location: /cart");
    exit;
    return $response;

});