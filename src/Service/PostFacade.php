<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\PostFullData;
use App\Model\ValueObject\PostId;
use App\Repository\Interfaces\CategoryRepositoryInterface;
use App\Repository\Interfaces\CommentRepositoryInterface;
use App\Repository\Interfaces\PostRepositoryInterface;
use App\Repository\Interfaces\TagRepositoryInterface;
use App\ValueObject\Pagination;

class PostFacade
{
    public function __construct(
        private PostRepositoryInterface $postRepo,
        private CategoryRepositoryInterface $categoryRepo,
        private TagRepositoryInterface $tagRepo,
        private CommentRepositoryInterface $commentRepo,
    ) {}

    public function getPostWithRelations(PostId $postId, Pagination $pagination): ?PostFullData
    {
        $post = $this->postRepo->getPost($postId);

        if (!$post) {
            return null;
        }

        $postTags = $this->tagRepo->getTagsByPost($postId);
        $postCategories = $this->categoryRepo->getCategoriesByPost($postId);
        $postComments = $this->commentRepo->getCommentsByPost($postId, $pagination);

        return new PostFullData($post, $postTags, $postCategories, $postComments);
    }
}