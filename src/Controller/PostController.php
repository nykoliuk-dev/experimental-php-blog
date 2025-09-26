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

        render('posts/index', [
            'title' => 'Список постов',
            'posts' => $posts,
        ]);
    }

    public function show(array $params): string
    {
        return 'один пост';
    }
    public function create(): string
    {
        return 'форма создания';
    }
    public function store(): string
    {
        return 'Добавить пост';
    }
    public function edit(array $params): string
    {
        return "Редактировать пост № {$params['id']}";
    }
    public function update(array $p): string
    {
        return 'сохранение изменений';
    }
    public function destroy(array $params): string
    {
        return "Удалить пост № {$params['id']}";
    }
}