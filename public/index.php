<?php
declare(strict_types=1);

use App\Controller\MainController;
use App\Controller\PostController;
use App\Repository\PostRepository;
use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Rakit\Validation\Validator;
use Twig\Environment;
use function FastRoute\simpleDispatcher;

$bootstrap = require_once dirname(__DIR__) . '/bootstrap/init.php';
$config = $bootstrap['config'];
$twig = $bootstrap['twig'];

session_start();

$builder = new ContainerBuilder();
$builder->addDefinitions([
    'config' => $config,
    Environment::class => $twig,
    PostRepository::class => DI\create()
        ->constructor($config['env']['db_path']),
    Validator::class => DI\autowire(),
    MainController::class => DI\autowire()
        ->constructorParameter('config', DI\get('config')),
    PostController::class => DI\autowire()
        ->constructorParameter('config', DI\get('config')),
]);
$container = $builder->build();

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
        $params = $routeInfo[2] ?? [];
        $controller = $container->get($class);
        echo $container->call([$controller, $method], ['params' => $params]);
        break;
}

