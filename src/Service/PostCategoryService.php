<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Repository\PostRepositoryInterface;

class PostCategoryService
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private TransactionManager $tx
    ) {}

    /**
     * @param CategoryId[] $categories
     */
    public function setPostCategories(PostId $postId, array $tags): void
    {
        $this->tx->wrap(function () use ($postId, $tags) {
            $this->posts->clearPostCategories($postId);
            $this->posts->addPostCategories($postId, $tags);
        });
    }
}