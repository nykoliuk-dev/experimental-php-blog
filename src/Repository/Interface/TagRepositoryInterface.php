<?php
declare(strict_types=1);

namespace App\Repository\Interface;

use App\Model\Tag;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;

interface TagRepositoryInterface
{
    /** @return Tag[] */
    public function getTags(): array;

    public function getTag(TagId $id): ?Tag;

    /** @return Tag[] */
    public function getTagsByPost(PostId $postId): array;

    public function addTag(Tag $tag): TagId;

    public function removeTag(TagId $id): bool;
}