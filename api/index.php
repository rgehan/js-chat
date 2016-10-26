<?php

require_once("./vendor/autoload.php");

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$pdo = new PDO("mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASS'));

