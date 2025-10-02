<?php
declare(strict_types=1);

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

define("APP", $config['paths']['app']);

return $config;