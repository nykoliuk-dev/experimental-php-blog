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

    public static function createWithoutSlug(
        ?int $id = null,
        ?int $userId = null,
        string $date = '2025-11-07',
        string $title = 'Title',
        string $content = 'Content',
        string $imageName = 'img.jpg'
    ): Post
    {
        $slug = null;

        return self::createPostUnsafely([
            'id' => $id,
            'userId' => $userId,
            'date' => $date,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'imageName' => $imageName
        ]);
    }

    private static function createPostUnsafely(array $data): Post
    {
        $reflection = new \ReflectionClass(Post::class);

        $post = $reflection->newInstanceWithoutConstructor();

        foreach ($data as $propertyName => $value){
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($post, $value);
        }

        return $post;
    }
}
