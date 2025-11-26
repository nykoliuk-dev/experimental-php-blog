<?php
declare(strict_types=1);

namespace App\Repository;

use App\Repository\Interface\PostMaintenanceRepositoryInterface;
use App\Service\DatabaseService;

/**
 * Repository for performing maintenance operations,
 * not included in the standard CRUD.
 */
class PostMaintenanceRepository implements PostMaintenanceRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    /**
     * Gets posts that don't have slugs.
     * Returns only the id and title needed for updating.
     * @return array<array-key, array> Array of database rows (id, title).
     */
    public function getPostsWithoutSlug(): array
    {
        $sql = "SELECT id, title FROM `posts` WHERE slug IS NULL OR slug = ''";
        return $this->db->fetchAll($sql);
    }

    /**
     * Updates the slug for a post by its ID.
     * @param int $id Post ID.
     * @param string $slug New slug.
     * @return bool True if the update is successful.
     */
    public function updatePostSlug(int $id, string $slug): bool
    {
        $sql = "UPDATE `posts` SET `slug` = :slug WHERE `id` = :id";
        $stmt = $this->db->query($sql, [
            'slug' => $slug,
            'id' => $id,
        ]);
        return $stmt->rowCount() > 0;
    }
}