<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Repository\PostRepositoryInterface;

class PostTagService
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private TransactionManager $tx
    ) {}

    /**
     * @param TagId[] $tags
     */
    public function setPostTags(PostId $postId, array $tags): void
    {
        $this->tx->wrap(function () use ($postId, $tags) {
            $this->posts->clearPostTags($postId);
            $this->posts->addPostTags($postId, $tags);
        });
    }
}