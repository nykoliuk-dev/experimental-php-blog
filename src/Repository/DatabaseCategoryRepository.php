<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
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

    public function getCategory(CategoryId $id): ?Category
    {
        $sql = "SELECT * FROM `categories` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id->value()]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToCategory($data);
    }

    /** @return Category[] */
    public function getCategoriesByPost(PostId $postId): array
    {
        $sql = "SELECT * FROM `categories` WHERE id IN(
                    SELECT category_id FROM `category_post` WHERE post_id=:id
        )";
        $rows = $this->db->fetchAll($sql, ['id' => $postId->value()]);

        return array_map([$this, 'mapRowToCategory'], $rows);
    }

    public function addCategory(Category $category): CategoryId
    {
        $sql = "INSERT INTO `categories` (parent_id, name, slug) 
        VALUES (:parent_id, :name, :slug)";

        $this->db->query($sql, [
            'parent_id' => $category->getParentId()?->value(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
        ]);

        return new CategoryId($this->db->lastInsertId());
    }

    public function removeCategory(CategoryId $id): bool
    {
        $sql = "DELETE FROM `categories` WHERE `id` = (:id)";
        $stmt = $this->db->query($sql, ['id' => $id->value()]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToCategory(array $row): Category
    {
        $parentId = !empty($row['parent_id']) ? new CategoryId((int)$row['parent_id']) : null;

        return new Category(
            id: new CategoryId((int)$row['id']),
            parentId: $parentId,
            name: $row['name'],
            slug: $row['slug'],
        );
    }
}