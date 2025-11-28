<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\Interface\PostCategoryServiceInterface;

class PostCategoryService implements PostCategoryServiceInterface
{
    public function __construct(
        private PostRepositoryInterface $posts,
    ) {}

    /**
     * @param CategoryId[] $categoryIds
     */
    public function setPostCategories(PostId $postId, array $categoryIds): void
    {
        $this->posts->clearPostCategories($postId);
        $this->posts->addPostCategories($postId, $categoryIds);
    }
}