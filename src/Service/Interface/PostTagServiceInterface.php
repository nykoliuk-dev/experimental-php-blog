<?php
declare(strict_types=1);

namespace App\Service\Interface;

use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;

interface PostTagServiceInterface
{
    /**
     * @param TagId[] $tagIds
     */
    public function setPostTags(PostId $postId, array $tagIds): void;
}