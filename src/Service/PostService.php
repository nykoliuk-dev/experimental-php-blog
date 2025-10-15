<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Post;
use App\Repository\PostRepository;

class PostService
{
    public function __construct(private PostRepository $repo)
    {
    }

    public function createPost(string $title, string $content, string $imageName): int
    {
        $id = $this->repo->newPostId();

        $post = new Post(
            $id,
            date('Y-m-d H:i:s'),
            $title,
            $content,
            $imageName,
        );

        return $this->repo->addPost($post);
    }
}