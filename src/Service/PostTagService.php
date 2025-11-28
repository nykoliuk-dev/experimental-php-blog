<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\Interface\PostTagServiceInterface;

class PostTagService  implements PostTagServiceInterface
{
    public function __construct(
        private PostRepositoryInterface $posts,
    ) {}

    /**
     * @param TagId[] $tagIds
     */
    public function setPostTags(PostId $postId, array $tagIds): void
    {
        $this->posts->clearPostTags($postId);
        $this->posts->addPostTags($postId, $tagIds);
    }
}