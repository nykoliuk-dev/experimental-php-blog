<?php
declare(strict_types=1);

use App\Command\MigrateCommand;
use App\Repository\DatabasePostRepository;
use App\Repository\JsonPostRepository;
use App\Service\DatabaseService;
use Symfony\Component\Console\Application;

$bootstrap = require_once dirname(__DIR__) . '/bootstrap/init.php';
$config = $bootstrap['config'];

$dsn = $config->getDsn();
$user = $config->env['DB_USER'];
$pass = $config->env['DB_PASS'];

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$dbServ = new DatabaseService($pdo);

$jsonRepo = new JsonPostRepository($config->dbPath());
$dbRepo   = new DatabasePostRepository($dbServ);

$migrationService = new \App\Service\PostMigrationService($jsonRepo, $dbRepo);

$app = new Application('Basic Blog CLI', '1.0.0');
$app->add(new MigrateCommand($migrationService));
$app->run();