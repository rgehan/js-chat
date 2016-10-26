<?php

require_once("./vendor/autoload.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Crée l'api RESTful
$app = new Silex\Application();
$app['debug'] = true;

//Le parser de fichier .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

//Creation de la db
$pdo = new PDO("mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'));

$app->get('/messages', function() use ($pdo) {
	$query = $pdo->query("SELECT * FROM messages");
	$data = $query->fetchAll(PDO::FETCH_ASSOC);

	return json_encode($data);
});

$app->post('/messages', function(Request $request) use ($pdo) {
	$message = $request->get('message');
	$uid = $request->get('uid');

	if($message == NULL || $uid == NULL)
		return new Response('Bad parameters', 500);

	$query = $pdo->prepare("INSERT INTO messages (id, uid, message) VALUES (NULL, :uid, :msg);");
	$ret = $query->execute(['uid' => $uid, 'msg' => $message]);

	if($ret)
		return new Response('Message posted.', 201);
	else
		return new Response('Message couldn\'t be posted', 500);
});

$app->run();