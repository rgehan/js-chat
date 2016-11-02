<?php

require_once("./vendor/autoload.php");

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

//Fonction middleware d'authentification
$authFunction = function(Request $request, Application $app) use ($pdo){
	$user = $request->server->get('PHP_AUTH_USER');
	$pw = $request->server->get('PHP_AUTH_PW');

	//On recupere le salt correspondant à l'utilisateur
	$saltRequest = $pdo->prepare("SELECT salt FROM users WHERE pseudo = :pseudo");
	$saltRequest->execute(['pseudo' => $user]);
	$data = $saltRequest->fetch(PDO::FETCH_ASSOC);

	//Si on a pas de salt
	if(!$data)
	{
		return new Response("Unauthorized (no such account ".$user.")", 401);
	}
	else
	{
		//On hash le password donné avec ce salt
		$salt = $data['salt'];
		$hash = hash('sha256', $salt . $pw);

		//Et on le compare a celui existant en db
		$passRequest = $pdo->prepare("SELECT COUNT(*) FROM users WHERE pseudo = :pseudo AND password = :hash");
		$passRequest->execute(['pseudo' => $user, 'hash' => $hash]);
	
		$count = $passRequest->fetch()[0];

		//Si on a pas d'utilisateur qui match, on ne poursuit pas...
		if($count != 1)
		{
			return new Response("Unauthorized (bad password)", 401);
		}

	}
};

//Recuperation des messages
$app->get('/messages', function() use ($pdo) {
	$query = $pdo->query("SELECT m.uid, m.message, u.pseudo FROM messages m INNER JOIN users u ON u.uid = m.uid ORDER BY m.id ASC");
	$data = $query->fetchAll(PDO::FETCH_ASSOC);

	return json_encode($data);
})
->before($authFunction);

//Ajout d'un message
$app->post('/messages', function(Request $request) use ($pdo) {
	$message = $request->get('message');

	if($message == NULL)
		return new Response('Bad parameters', 500);

	//L'utilisateur est forcément logué, on recupere son uid
	$user = $request->server->get('PHP_AUTH_USER');
	$uidQuery = $pdo->prepare("SELECT uid FROM users WHERE pseudo = :pseudo");
	$uidQuery->execute(['pseudo' => $user]);
	$uid = $uidQuery->fetch(PDO::FETCH_ASSOC)['uid'];

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
$app->post('/utilisateurs', function(Request $request) use ($pdo) {
	$user = $request->get('user');
	$pw = $request->get('pw');

	if($user == NULL || $pw == NULL)
		return new Response('Bad parameters', 500);

	$salt = uniqid(mt_rand(), true);
	$hash = hash('sha256', $salt . $pw);

	$query = $pdo->prepare("INSERT INTO users (uid, pseudo, password, salt) VALUES (NULL, :pseudo, :pass, :salt);");
	$ret = $query->execute(['pseudo' => $user, 'pass' => $hash, 'salt' => $salt]);

	if($ret)
		return new Response('User added.', 201);
	else
		return new Response('User couldn\'t be added', 500);
})
->before($authFunction);

//Censé authoriser les requetes OPTIONS
$app->match("{url}", function($url) use ($app){
	return "OK OPTIONS"; 
})
->assert('url', '.*')
->method("OPTIONS");

$app->run();