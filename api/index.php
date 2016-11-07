<?php

require_once("./vendor/autoload.php");
require_once("Auth.class.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

//Le parser de fichier .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

//Creation de la db
$pdo = new PDO("mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'));

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: DELETE, GET, HEAD, POST, PUT, OPTIONS, TRACE");

/* 
 * Crée l'api RESTful
 */
$app = new Application();
$app['debug'] = true;


$auth = new Auth($pdo);


//Middleware d'authentification
$authFunction = function(Request $request, Application $app) use ($pdo, $auth){
	$token = $request->cookies->get('auth_token');

	$authData = $auth->checkToken($token);

	//Invalid token
	if(!$authData)
		return new Response("Unauthorized", 401);

	//On passe les infos de l'user au reste des fonctions
	$request->attributes->set('userData', ['uid' => $authData['uid']]);
};



//Recuperation des messages
$app->get('/messages', function() use ($pdo) {
	$query = $pdo->query("SELECT m.uid, m.message, u.pseudo FROM messages m INNER JOIN users u ON u.uid = m.uid ORDER BY m.id ASC");
	$data = $query->fetchAll(PDO::FETCH_ASSOC);

	return json_encode($data);
})
->before($authFunction);



//Ajout d'un message
$app->post('/messages', function(Request $request) use ($pdo, $auth) {
	$message = $request->get('message');

	if($message == NULL)
		return new Response('Bad parameters', 500);

	$uid = $request->attributes->get('userData')['uid'];

	//insertion du message
	$query = $pdo->prepare("INSERT INTO messages (id, uid, message) VALUES (NULL, :uid, :msg);");
	$ret = $query->execute(['uid' => $uid, 'msg' => $message]);

	if($ret)
		return new Response('Message posted.', 201);
	else
		return new Response('Message couldn\'t be posted (uid='.$uid.')', 500);
})
->before($authFunction);


//Ajout d'un utilisateur
$app->post('/utilisateurs', function(Request $request) use ($pdo, $auth){
	$user = $request->get('user');
	$pw = $request->get('pw');

	if($user == NULL || $pw == NULL)
		return new Response('Bad parameters', 500);

	$ret = $auth->saveUser($user, $pw);

	if($ret)
		return new Response('User added.', 201);
	else
		return new Response('User couldn\'t be added', 500);
})
->before($authFunction);



//Login
$app->post('/login', function(Request $request) use ($pdo, $auth){
	$user = $request->get('user');
	$pw = $request->get('pw');

	if($user == NULL || $pw == NULL)
		return new Response('Bad parameters ('.$user.';'.$pw.')', 500);

	if($auth->checkUserSuppliedCredentials($user, $pw))
	{
		//On genere un token pour l'utilisateur et on lui envoit
		$token = $auth->generateUserToken($user);

		if($token)
			return json_encode(['status' => 'login_ok', 'token' => $token]);
		else
			return json_encode(['status' => 'login_fail', 'reason' => 'no token could be generated']);
	}

	//On lui indique le login a echoue
	return json_encode(['status' => 'login_fail', 'reason' => 'invalid credentials']);
});



//Censé authoriser les requetes OPTIONS
$app->match("{url}", function($url) use ($app){
	return "OK OPTIONS"; 
})
->assert('url', '.*')
->method("OPTIONS");

$app->run();