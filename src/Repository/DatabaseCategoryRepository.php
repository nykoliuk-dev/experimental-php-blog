<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;
use App\Service\DatabaseService;

class DatabaseCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    /** @return Category[] */
    public function getCategories(): array
    {
        $sql = "SELECT * FROM `categories` ORDER BY `name`";
        $rows = $this->db->fetchAll($sql);

        return array_map([$this, 'mapRowToCategory'], $rows);
    }

    public function getCategory(int $id): ?Category
    {
        $sql = "SELECT * FROM `categories` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToCategory($data);
    }

    /** @return Category[] */
    public function getCategoriesByPost(int $postId): array
    {
        $sql = "SELECT * FROM `categories` WHERE id IN(
                    SELECT category_id FROM `category_post` WHERE post_id=:id
        )";
        $rows = $this->db->fetchAll($sql, ['id' => $postId]);

        return array_map([$this, 'mapRowToCategory'], $rows);
    }

    public function addCategory(Category $category): int
    {
        $sql = "INSERT INTO `categories` (parent_id, name, slug) 
        VALUES (:parent_id, :name, :slug)";

        $this->db->query($sql, [
            'parent_id' => $category->getParentId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ]);

        return $this->db->lastInsertId();
    }

    public function removeCategory(int $id): bool
    {
        $sql = "DELETE FROM `categories` WHERE `id` = (:id)";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToCategory(array $row): Category
    {
        $parentId = !empty($row['parent_id']) ? (int)$row['parent_id'] : null;

        return new Category(
            id: (int)$row['id'],
            parentId: $parentId,
            name: $row['name'],
            slug: $row['slug'],
        );
    }
}