<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;

interface PostRepositoryInterface
{
    /** @return Post[] */
    public function getPosts(): array;

    public function getPost(int $id): ?Post;

    public function addPost(Post $post): int;

    public function removePost(int $id): bool;
}