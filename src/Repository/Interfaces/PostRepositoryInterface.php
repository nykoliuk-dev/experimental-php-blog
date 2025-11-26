<?php
declare(strict_types=1);

namespace App\Repository\Interfaces;

use App\Model\Post;
use App\Model\ValueObject\PostId;

interface PostRepositoryInterface
{
    /** @return Post[] */
    public function getPosts(): array;

    public function getPost(PostId $id): ?Post;

    public function addPost(Post $post): PostId;

    public function removePost(PostId $id): bool;

    public function clearPostTags(PostId $postId): bool;

    public function clearPostCategories(PostId $postId): bool;

    public function addPostTags(PostId $postId, array $tagIds): void;

    public function addPostCategories(PostId $postId, array $categoryIds): void;
}