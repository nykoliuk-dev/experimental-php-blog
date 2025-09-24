<?php
declare(strict_types=1);

namespace App\Controller;

class PostController
{
    public function index(): string  // список постов
    {
        return 'Список постов';
    }

    public function add(): string    // форма + добавление
    {
        return 'Добавить пост';
    }

    public function edit(array $params): string
    {
        return "Редактировать пост № {$params['id']}";
    }

    public function delete(array $params): string
    {
        return "Удалить пост № {$params['id']}";
    }
}