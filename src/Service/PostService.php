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

    public function createPost(string $title, string $content, string $imageName): int
    {
        $post = new Post(
            null,
            date('Y-m-d H:i:s'),
            $title,
            $content,
            $imageName,
        );

        return $this->repo->addPost($post);
    }
}