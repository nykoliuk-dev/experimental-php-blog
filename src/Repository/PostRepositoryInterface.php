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

    public function setPostTags(int $postId, array $tagIds): void;

    public function setPostCategories(int $postId, array $categoryIds): void;

    /** @return int[]  Array of tag IDs */
    public function getPostTags(int $postId): array;

    /** @return int[]  Array of category IDs */
    public function getPostCategories(int $postId): array;
}