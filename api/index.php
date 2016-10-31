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

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: DELETE, GET, HEAD, POST, PUT, OPTIONS, TRACE");

$app->get('/messages', function() use ($pdo) {
	$query = $pdo->query("SELECT m.uid, m.message, u.pseudo FROM messages m INNER JOIN users u ON u.uid = m.uid ORDER BY m.id ASC");
	$data = $query->fetchAll(PDO::FETCH_ASSOC);

	return json_encode($data);
});

$app->post('/messages', function(Request $request) use ($pdo) {
	$message = $request->get('message');
	$uid = $request->get('uid');

	if($message == NULL || $uid == NULL)
		return new Response('Bad parameters ('.$message.';'.$uid.')', 500);

	$query = $pdo->prepare("INSERT INTO messages (id, uid, message) VALUES (NULL, :uid, :msg);");
	$ret = $query->execute(['uid' => $uid, 'msg' => $message]);

	if($ret)
		return new Response('Message posted.', 201);
	else
		return new Response('Message couldn\'t be posted', 500);
});

$app->match("{url}", function($url) use ($app){
	return "OK OPTIONS"; 
})
->assert('url', '.*')
->method("OPTIONS");

$app->run();