<?php
declare(strict_types=1);

define("ROOT", dirname(__DIR__));

require_once ROOT . '/vendor/autoload.php';

// Loading .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

define("DEBUG", (bool) $_ENV['DEBUG']);
define("DB_PATH", ROOT . $_ENV['DB_PATH']);
if (!defined('DB_PATH') || empty(DB_PATH)) {
    throw new RuntimeException('DB_PATH not set in .env');
}
define("CACHE", ROOT . '/tmp/cache');
define("CONFIG", ROOT . '/config');
define("APP", ROOT . '/src');
define("HELPERS", APP . '/Helpers');

require_once HELPERS . '/functions.php';