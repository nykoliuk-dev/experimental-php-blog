<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$config = require_once dirname(__DIR__) . '/bootstrap/init.php';

$routes = require_once $config['paths']['config'] . '/routes.php';

$dispatcher = simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars    = $routeInfo[2];
        $repo = new \App\Repository\PostRepository($config['env']['db_path']);
        $controller = new $class($config, $repo);
        echo $controller->$method($vars ?? []);
        break;
}

