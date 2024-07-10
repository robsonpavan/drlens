<?php 

//Iniciando o uso de sessão
if (!isset($_SESSION)) {
    session_start();
}

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use rapinformatica\Page;
use rapinformatica\PageAdmin;
use rapinformatica\Model\User;
require_once("vendor/autoload.php");


$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);


// Rota padrão do site (caminho /)
$app->get('/', function (Request $request, Response $response, $args) {
    /*$name = $args['name'];
    $response->getBody()->write("Hello, $name");*/
	$page = new Page();

	$page->setTpl("index");

    return $response;
}); 

// Rota para interface administrativa
$app->get('/admin', function (Request $request, Response $response, $args) {
    
	//Metodo estático para verificar (testar) o login do uauário
    User::verifyLogin();
    
    $page = new PageAdmin();

	$page->setTpl("index");

    return $response;
});

// Rota para página de login da interface administrativa
$app->get('/admin/login', function (Request $request, Response $response, $args) {
	
    //$name = $args['name'];
    //$response->getBody()->write("Hello, $name");
	
	$page = new PageAdmin([
        "header"=> false,
        "footer"=> false
    ]);

	$page->setTpl("login");

    return $response;
});

// Rota para receber, via POST, as informações de autenticação
$app->post('/admin/login', function (Request $request, Response $response, $args) {
	echo "3";
    //$name = $args['name'];
    //$response->getBody()->write("Hello, $name");
	
	//Executando método estático login da classe User para realizar a autenticação do usuário (estão sendo passados o login e a senha capturados no formulário de login
    //A váriável $_POST["login"] captura o login do usuário e o parâemtro login passado na variável é o nome do campo que recebe o login (nome do campo input da página html)
    User::login($_POST["login"], $_POST["password"]);

    //Redirecionando para página principal da interface de administração (index.html) caso a autenticação tenha sido bem sucedida
    header("Location: /admin");
    exit; //Parando a execução

    return $response;
});

//Rota para página de logout
$app->get("/admin/logout", function (Request $request, Response $response, $args) {

    //Executando o método estático para realizar logout
    User::logout();
    //Redirecionando para página de login
    header("Location: /admin/login");
    //Parando a execução para que a próxima página seja carregada
    exit;

    return $response;
});

// Run app
$app->run();

 ?>