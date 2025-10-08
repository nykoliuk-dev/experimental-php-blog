<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PostRepository;
use Rakit\Validation\Validator;

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
    public function show(array $params): string
    {
        return 'один пост';
    }
    public function create(): void
    {
        $this->render('posts/create', [
            'title' => 'Добавить статью',
        ]);
    }
    public function store(Validator $validator): string
    {
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        return 'Добавить пост';
    }
}