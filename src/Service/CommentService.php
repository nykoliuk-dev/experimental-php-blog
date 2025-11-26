<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Comment;
use App\Model\ValueObject\CommentId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use App\Repository\Interfaces\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $repo,
    )
    {
    }

    public function createComment(PostId $postId, ?UserId $userId, string $content): CommentId
    {
        return $this->repo->addComment(new Comment(
            id: null,
            postId: $postId,
            userId: $userId,
            content: $content,
            createdAt: date('Y-m-d H:i:s'),
        ));
    }
}