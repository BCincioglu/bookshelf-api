<?php

require_once 'vendor/autoload.php';
require_once 'util/auth.php';
require_once 'util/validation.php';
require_once 'conf/db.php';
require_once 'controller/bookController.php';
require_once 'router/routerBook.php';


use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// API Key Check
checkApiKey();

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

// Routing
routeRequestBook($method, $request, $input);

?>
