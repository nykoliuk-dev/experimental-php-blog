<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Post;
use App\Repository\PostRepositoryInterface;

class PostService
{
    public function __construct(private PostRepositoryInterface $repo)
    {
    }

    public function createPost(?int $userId, string $title, string $content, string $imageName): int
    {
        $slug = $this->generateSlug($title);

        $post = new Post(
            id: null,
            userId: $userId,
            date: date('Y-m-d H:i:s'),
            title: $title,
            slug: $slug,
            content: $content,
            imageName: $imageName,
        );

        return $this->repo->addPost($post);
    }

    private function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}