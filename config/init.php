<?php
declare(strict_types=1);

define("ROOT", dirname(__DIR__));

require_once ROOT . '/vendor/autoload.php';

// Loading .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

define("DEBUG", (bool) $_ENV['DEBUG']);
define("CACHE", ROOT . '/tmp/cache');
define("CONFIG", ROOT . '/config');