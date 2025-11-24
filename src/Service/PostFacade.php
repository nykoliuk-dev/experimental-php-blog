<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\PostFullData;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Repository\TagRepositoryInterface;

class PostFacade
{
    public function __construct(
        private PostRepositoryInterface $postRepo,
        private CategoryRepositoryInterface $categoryRepo,
        private TagRepositoryInterface $tagRepo,
    ) {}

    public function getPostWithRelations(int $postId): ?PostFullData
    {
        $post = $this->postRepo->getPost($postId);

        if (!$post) {
            return null;
        }

        $postTags = $this->tagRepo->getTagsByPost($postId);
        $postCategories = $this->categoryRepo->getCategoriesByPost($postId);

        return new PostFullData($post, $postTags, $postCategories);
    }
}