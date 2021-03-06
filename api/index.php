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
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token");
header("Access-Control-Allow-Methods: DELETE, GET, HEAD, POST, PUT, OPTIONS, TRACE");

/* 
 * Crée l'api RESTful
 */
$app = new Application();
$app['debug'] = true;


$auth = new Auth($pdo);


//Middleware d'authentification
$authFunction = function(Request $request, Application $app) use ($pdo, $auth){
	
	//Pas de jeton ?
	if(!$request->headers->has('x-auth-token'))
		return new Response("Unauthorized (no token)", 401);


	$token = explode(' ', $request->headers->get('x-auth-token'))[1];
	$authData = $auth->checkToken($token);

	//Invalid token
	if(!$authData)
		return new Response("Unauthorized", 401);

	//On passe les infos de l'user au reste des fonctions
	$request->attributes->set('userData', ['uid' => $authData['uid']]);
};



//Recuperation des messages
$app->get('/messages', function(Request $request) use ($pdo) {
	$convId = $request->get('convId');

	$query = $pdo->prepare("SELECT m.uid, m.message, u.pseudo 
							FROM messages m 
							INNER JOIN users u 
						  		ON u.uid = m.uid
							WHERE m.conv_id = :convId
							ORDER BY m.id ASC");
	$query->execute(['convId' => $convId]);

	$data = $query->fetchAll(PDO::FETCH_ASSOC);

	return json_encode($data);
})
->before($authFunction);



//Ajout d'un message
$app->post('/messages', function(Request $request) use ($pdo, $auth) {
	$message = $request->get('message');
	$convId = $request->get('convId');

	if($message == NULL)
		return new Response('Bad parameters', 500);

	$uid = $request->attributes->get('userData')['uid'];

	//insertion du message
	$query = $pdo->prepare("INSERT INTO messages (id, uid, message, conv_id, date_added) VALUES (NULL, :uid, :msg, :conv, NOW());");
	$ret = $query->execute(['uid' => $uid, 'msg' => $message, 'conv' => $convId]);

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


//Recupere des infos sur l'utilisateur connecté
$app->get('/userinfo', function(Request $request) use ($pdo){
	$uid = $request->attributes->get('userData')['uid'];

	$query = $pdo->prepare("SELECT uid, pseudo FROM users WHERE uid = :uid");
	$query->execute(['uid' => $uid]);

	return new Response(json_encode($query->fetch(PDO::FETCH_ASSOC)), 200);
})
->before($authFunction);


//Recupere des infos sur les conversations de l'utilisateur
$app->get('/conversations', function(Request $request) use ($pdo){
	$uid = $request->attributes->get('userData')['uid'];

	$query = $pdo->prepare("SELECT a.message AS lastMessage, 
								   a.conv_id, 
								   a.date_added as lastDate
							FROM messages a
							INNER JOIN (SELECT MAX(id) AS id
									    FROM messages
									    GROUP BY conv_id) AS b
							ON a.id = b.id
							WHERE a.conv_id IN (SELECT conv_id FROM messages
												WHERE uid = :uid
												GROUP BY conv_id)
							ORDER BY lastDate DESC");
	$query->execute(['uid' => $uid]);

	return new Response(json_encode($query->fetchAll(PDO::FETCH_ASSOC)), 200);
})
->before($authFunction);


//Censé authoriser les requetes OPTIONS
$app->match("{url}", function($url) use ($app){
	return "OK OPTIONS"; 
})
->assert('url', '.*')
->method("OPTIONS");

$app->run();



