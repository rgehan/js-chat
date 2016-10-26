<?php

require_once("./vendor/autoload.php");

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

$app->run();