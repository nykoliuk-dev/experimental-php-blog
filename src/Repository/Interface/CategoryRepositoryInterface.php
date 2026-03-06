<?php
declare(strict_types=1);

namespace App\Repository\Interface;

use App\Model\Category;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function getCategories(): array;

    public function getCategory(CategoryId $id): ?Category;

    /** @return Category[] */
    public function getCategoriesByPost(PostId $postId): array;

    public function addCategory(Category $category): CategoryId;

    public function removeCategory(CategoryId $id): bool;
}