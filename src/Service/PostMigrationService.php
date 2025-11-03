<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepositoryInterface;

class PostMigrationService
{
    /**
     * @param PostRepositoryInterface $sourceRepo JSON repository (source)
     * @param PostRepositoryInterface $targetRepo Database repository (target)
    */
    public function __construct(
        private PostRepositoryInterface $jsonRepo,
        private PostRepositoryInterface $dbRepo
    ) {}

    public function migrate(): int
    {
        $posts = $this->jsonRepo->getPosts();
        $count = 0;

        foreach ($posts as $post) {
            if ($this->isValid($post) && !$this->exists($post->getId())) {
                $this->dbRepo->addPost($post);
                $count++;
            }
        }

        return $count;
    }

    private function isValid(Post $post): bool
    {
        return $post->getTitle() !== '' && $post->getContent() !== '';
    }

    private function exists(int $id): bool
    {
        return $this->dbRepo->getPost($id) !== null;
    }
}