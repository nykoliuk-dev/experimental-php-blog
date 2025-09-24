<?php
declare(strict_types=1);

use App\Core\Router;

require_once dirname(__DIR__) . '/config/init.php';

$router = new Router();

$routes = require CONFIG . '/routes.php';
$routes($router);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

