<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PostRepository;

class MainController extends Controller
{
    public function index(): void
    {
        $posts = $this->repo->getPosts();

        render('main/index', [
            'title' => 'Главная страница',
            'posts' => $posts,
        ]);
    }
}