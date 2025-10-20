<?php
declare(strict_types=1);

use App\Repository\PostRepository;
use App\Service\FileUploaderInterface;
use App\Service\LocalFileUploader;
use DI\ContainerBuilder;
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
        ->constructor($config->dbPath()),
    Validator::class => DI\autowire(),
    FileUploaderInterface::class => DI\get(LocalFileUploader::class),
    LocalFileUploader::class => DI\create()
        ->constructor($config->gallery),
]);
$container = $builder->build();

$routes = require_once $config->configDir . '/routes.php';

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

