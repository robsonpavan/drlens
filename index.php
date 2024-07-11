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

//Rota para listar os usuários
$app->get("/admin/users", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin();

	$page->setTpl("users", array(
        "users"=> $users
    ));

    return $response;
});

//Rota para criar usuários
$app->get("/admin/users/create", function (Request $request, Response $response, $args) {

    User::verifyLogin();
    
    $page = new PageAdmin();

	$page->setTpl("users-create");

    return $response;
});

//Rota para deletar um usuário
$app->get('/admin/users/{iduser}/delete', function (Request $request, Response $response, $args ) {

    User::verifyLogin();

    $iduser = $args['iduser'];
    
    $user = new User();
    
    //Buscando as informações do usuário existentes no BD
    $user->get((int)$iduser);
    
    //Excluindo o usuário carregado no passo anterior
    $user->delete();
    
    //Encaminhando para página que lista os usuário após inserção do novo usuário
    header("Location: /admin/users");
    exit;

    return $response;
});

//Rota para Alterar os dados de um usuário
$app->get("/admin/users/{iduser}", function (Request $request, Response $response, $args) {

    $iduser = $args['iduser'];

    User::verifyLogin();

    $user = new User;

    $user->get((int)$iduser);
       
    $page = new PageAdmin();

	$page->setTpl("users-update",  array(
        "user"=>$user->getValues()
    ));

    return $response;
});

//Rota para consolidar/salvar as informações de inclusão de um usuário
$app->post("/admin/users/create", function (Request $request, Response $response, $args) {

    User::verifyLogin();
    
    $user = new User();

    //Transformando o true ou false do checkbox em 1 ou 0 para armazenar no BD
    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    //Ajuste no código para que o login com novos usuários funcione enquanto a criptografia de senha não é implementada
    //$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
      //  "cost" => 12
   // ]);
    
    $user->setData($_POST);

    $user->save();
    
    //Encaminhando para página que lista os usuário após inserção do novo usuário
    header("Location: /admin/users");
    exit;
    

    return $response;
});

//Rota para salvar as alterações dos dados de um usuário
$app->post("/admin/users/{iduser}", function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $iduser = $args['iduser'];
    
    //Metodo estático para verificar (testar) o login do uauário
    User::verifyLogin();
    
    $user = new User();
    
    //Carregando as informações existentes do usuários no BD
    $user->get((int)$iduser);
    
    //Transformando o true ou false do checkbox em 1 ou 0 para armazenar no BD
    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    
    //Buscando as informações passadas pelo form (HTML) via post
    $user->setData($_POST);
    
    $user->update();
    
    //Encaminhando para página que lista os usuário após inserção do novo usuário
    header("Location: /admin/users");
    exit;
   

    return $response;
});

//Rota para recuperar a senha dos usuários
$app->get("/admin/forgot", function (Request $request, Response $response, $args) {

    $page = new PageAdmin([
        "header"=> false,
        "footer"=> false
    ]);

	$page->setTpl("forgot");

    return $response;
});

//Rota para enviar o e-mail
$app->post("/admin/forgot", function (Request $request, Response $response, $args) {

	//Método da estático classe User para receber o e-mail do usuário passado pela pági a do esqueci a senha (forgot)
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

    return $response;

});

// Run app
$app->run();

 ?>