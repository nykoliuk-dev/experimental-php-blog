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

require_once $paths['helpers'] . '/functions.php';

$config = [
    'paths' => $paths,
    'env'   => $env,
];

return $config;