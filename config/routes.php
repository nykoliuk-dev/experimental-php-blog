<?php
declare(strict_types=1);

use App\Core\Router;

/** @var Router $router */
return static function (Router $router): void {
    $router->get('/', fn() => 'Главная страница');
    $router->get('/posts', fn() => 'Список постов');
    $router->get('/post/{id}', function (array $params) {
        return "Просмотр поста № {$params['id']}";
    });
};