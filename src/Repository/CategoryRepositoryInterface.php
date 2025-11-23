<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function getCategories(): array;

    public function getCategory(int $id): ?Category;

    /** @return Category[] */
    public function getCategoriesByPost(int $postId): array;

    public function addCategory(Category $category): int;

    public function removeCategory(int $id): bool;
}