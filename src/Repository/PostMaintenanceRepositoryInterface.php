<?php
declare(strict_types=1);

namespace App\Repository;

interface PostMaintenanceRepositoryInterface
{
    /**
     * Gets posts that don't have slugs.
     * Returns only the id and title needed for updating.
     * @return array<array-key, array> Array of database rows (id, title).
     */
    public function getPostsWithoutSlug(): array;

    /**
     * Updates the slug for a post by its ID.
     * @param int $id Post ID.
     * @param string $slug New slug.
     * @return bool True if the update is successful.
     */
    public function updatePostSlug(int $id, string $slug): bool;
}