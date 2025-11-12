<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\MigrationResult;
use App\Model\Post;
use App\Repository\PostRepositoryInterface;

class PostMigrationService
{
    /**
     * @param PostRepositoryInterface $sourceRepo JSON repository (source)
     * @param PostRepositoryInterface $targetRepo Database repository (target)
    */
    public function __construct(
        private PostRepositoryInterface $from,
        private PostRepositoryInterface $to
    ) {}

    /**
     * Migrates posts from JSON to the database.
     *
     * @return MigrationResult Result object containing success count and errors.
     */
    public function migrate(): MigrationResult
    {
        $posts = $this->from->getPosts();

        $migratedCount = 0;
        $criticalErrors = [];
        $validationErrors = [];

        foreach ($posts as $post) {
            if (!$this->isValid($post)){
                $validationErrors[] = "Post №{$post->getId()} is not valid";
                continue;
            }
            if ($this->exists($post->getId())) {
                $validationErrors[] = "Post №{$post->getId()} already exists";
                continue;
            }
            try {
                $this->to->addPost($post);
                $migratedCount++;
            } catch (\Throwable $e) {
                $criticalErrors[] = "Failed to migrate post №{$post->getId()}: {$e->getMessage()}";
            }
        }

        return new MigrationResult($migratedCount, $criticalErrors, $validationErrors);
    }

    private function isValid(Post $post): bool
    {
        return $post->getTitle() !== '' && $post->getContent() !== '';
    }

    private function exists(int $id): bool
    {
        return $this->to->getPost($id) !== null;
    }
}