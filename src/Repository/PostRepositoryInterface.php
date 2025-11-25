<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;
use App\Model\ValueObject\PostId;

interface PostRepositoryInterface
{
    /** @return Post[] */
    public function getPosts(): array;

    public function getPost(PostId $id): ?Post;

    public function addPost(Post $post): PostId;

    public function removePost(PostId $id): bool;

    public function setPostTags(PostId $postId, array $tagIds): void;

    public function setPostCategories(PostId $postId, array $categoryIds): void;
}