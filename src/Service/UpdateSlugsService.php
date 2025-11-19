<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\MigrationResult;
use App\Repository\PostMaintenanceRepositoryInterface;
use Throwable;

class UpdateSlugsService
{
    /**
     * @param PostMaintenanceRepositoryInterface $repo Database repository
     */
    public function __construct(private PostMaintenanceRepositoryInterface $repo)
    {
    }

    /**
     * Updates the slug for all posts that are missing one.
     * * @return MigrationResult Result of the operation (number of updated posts and errors).
     */
    public function updateMissingSlugs(): MigrationResult
    {
        $postsToUpdate = $this->repo->getPostsWithoutSlug();

        $updatedCount = 0;
        $criticalErrors = [];
        $validationErrors = [];

        foreach ($postsToUpdate as $row) {
            $id = (int) $row['id'];
            $title = $row['title'];

            try {
                $slug = $this->generateSlug($title);

                if (empty($slug)) {
                    $validationErrors[] = "Skipped post #{$id}: Generated slug is empty for title '{$title}'";
                    continue;
                }

                if ($this->repo->updatePostSlug($id, $slug)) {
                    $updatedCount++;
                } else {
                    $criticalErrors[] = "Failed to update slug for post #{$id}.";
                }

            } catch (Throwable $e) {
                $criticalErrors[] = "Critical error updating slug for post #{$id} ('{$title}'): {$e->getMessage()}";
            }
        }

        return new MigrationResult($updatedCount, $criticalErrors, $validationErrors);
    }

    /**
     * Generates a URL-safe slug from a string.
     * @param string $title Original string (post title).
     * @return string Generated slug.
     */
    private function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}