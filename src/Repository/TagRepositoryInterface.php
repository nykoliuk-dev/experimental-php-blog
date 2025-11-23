<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Tag;

interface TagRepositoryInterface
{
    /** @return Tag[] */
    public function getTags(): array;

    public function getTag(int $id): ?Tag;

    /** @return Tag[] */
    public function getTagsByPost(int $postId): array;

    public function addTag(Tag $tag): int;

    public function removeTag(int $id): bool;
}