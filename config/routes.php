<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Controller\PostController;

return function (RouteCollector $r): void {
    $r->addRoute('GET', '/', fn() => 'Главная страница');
    $r->addRoute('GET',  '/posts',          [PostController::class, 'index']);
    $r->addRoute('GET',  '/post/add',       [PostController::class, 'add']);
    $r->addRoute('GET',  '/post/{id:\d+}/edit', [PostController::class, 'edit']);
    $r->addRoute('POST', '/post/{id:\d+}/delete', [PostController::class, 'delete']);
};