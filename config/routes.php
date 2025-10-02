<?php
declare(strict_types=1);

use App\Controller\MainController;
use FastRoute\RouteCollector;
use App\Controller\PostController;

return function (RouteCollector $r): void {
    $r->addRoute('GET', '/', [MainController::class, 'index']);
    $r->addRoute('GET', '/posts', [PostController::class, 'index']);
};