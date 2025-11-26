<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\Interface\PostRepositoryInterface;

class MainController extends Controller
{
    public function index(PostRepositoryInterface $repo): void
    {
        $posts = $repo->getPosts();

        $this->render('main/index', [
            'title' => 'Список постов',
            'posts' => $posts,
        ]);
    }
}