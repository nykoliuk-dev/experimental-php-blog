<?php
declare(strict_types=1);

namespace App\Service\Interface;

use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;

interface PostCategoryServiceInterface
{
    /**
     * @param CategoryId[] $categoryIds
     */
    public function setPostCategories(PostId $postId, array $categoryIds): void;
}