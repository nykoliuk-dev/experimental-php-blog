<?php
declare(strict_types=1);

namespace Tests\Factory;

use App\Model\Post;

class PostFactory
{
    public static function create(
        ?int $id = null,
        ?int $userId = null,
        string $date = '2025-11-07',
        string $title = 'Title',
        string $slug = 'title',
        string $content = 'Content',
        string $imageName = 'img.jpg'
    ): Post {
        return new Post(
            id: $id,
            userId: $userId,
            date: $date,
            title: $title,
            slug: $slug,
            content: $content,
            imageName: $imageName
        );
    }
}
