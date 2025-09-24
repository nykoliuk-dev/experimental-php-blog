<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepository;

class MainController
{
    public function index(): void
    {
        $repo = new PostRepository(DB_PATH);
        $posts = $repo->getPosts();

        render('main/index', [
            'title' => 'Главная страница',
            'posts' => $posts,
        ]);
    }
}