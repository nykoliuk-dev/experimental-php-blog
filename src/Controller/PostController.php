<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PostRepository;

class PostController extends Controller
{
    public function index(): void
    {
        $posts = $this->repo->getPosts();

        $this->render('posts/index', [
            'title' => 'Список постов',
            'posts' => $posts,
        ]);
    }
}