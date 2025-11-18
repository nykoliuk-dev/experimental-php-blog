<?php
declare(strict_types=1);

use App\Core\Config;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

// Loading .env
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();
$dotenv->required('DB_PATH')->notEmpty();

$paths = require $root . '/config/paths.php';

require_once $paths['helpers'] . '/functions.php';

$config = new Config(
    root: $paths['root'],
    cache: $paths['cache'],
    configDir: $paths['config'],
    app: $paths['app'],
    view: $paths['view'],
    helpers: $paths['helpers'],
    gallery: $paths['gallery'],
    env: $_ENV,
);

$loader = new FilesystemLoader($paths['view']);
$twig = new Environment($loader, [
    'cache' => $paths['cache'] . '/twig',
    'debug' => (bool)$_ENV['DEBUG'],
]);

return [
    'config' => $config,
    'twig'   => $twig,
];