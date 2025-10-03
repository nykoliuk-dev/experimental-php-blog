<?php
declare(strict_types=1);

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

// Loading .env
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();
$dotenv->required('DB_PATH')->notEmpty();

$paths = require $root . '/config/paths.php';

$env = require $root . '/config/env.php';
$env['db_path'] = $paths['root'] . $env['db_path'];

require_once $paths['helpers'] . '/functions.php';

$config = [
    'paths' => $paths,
    'env'   => $env,
];

$loader = new FilesystemLoader($paths['view']);
$twig = new Environment($loader, [
    'cache' => $paths['cache'] . '/twig',
    'debug' => $env['debug'],
]);

return [
    'config' => $config,
    'twig'   => $twig,
];