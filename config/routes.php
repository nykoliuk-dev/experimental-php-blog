<?php
declare(strict_types=1);

use App\Controller\Auth\LoginController;
use App\Controller\Auth\LogoutController;
use App\Controller\Auth\RegisterController;
use App\Controller\CommentController;
use App\Controller\MainController;
use FastRoute\RouteCollector;
use App\Controller\PostController;

return function (RouteCollector $r): void {
    // Main
    $r->addRoute('GET', '/', [MainController::class, 'index']);

    // Posts
    $r->addRoute('GET', '/posts', [PostController::class, 'index']);
    $r->addRoute('GET', '/posts/{id:\d+}', [PostController::class, 'show']);
    $r->addRoute('GET', '/posts/create', [PostController::class, 'create']);
    $r->addRoute('POST', '/posts', [PostController::class, 'store']);

    // Auth — Registration
    $r->addRoute('GET', '/register', [RegisterController::class, 'showRegisterForm']);
    $r->addRoute('POST', '/register', [RegisterController::class, 'register']);

    // Auth — Login
    $r->addRoute('GET', '/login', [LoginController::class, 'showForm']);
    $r->addRoute('POST', '/login', [LoginController::class, 'login']);

    // Auth — Logout
    $r->addRoute('POST', '/logout', [LogoutController::class, 'logout']);

    // Comments
    $r->addRoute('GET', '/posts/{postId:\d+}/comments', [CommentController::class, 'index']);
    $r->addRoute('POST', '/posts/{postId:\d+}/comments', [CommentController::class, 'store']);
    $r->addRoute('DELETE', '/comments/{id:\d+}', [CommentController::class, 'destroy']);
};