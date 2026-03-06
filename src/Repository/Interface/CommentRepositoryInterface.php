<?php
declare(strict_types=1);

namespace App\Repository\Interface;

use App\Model\Comment;
use App\Model\ValueObject\CommentId;
use App\Model\ValueObject\PostId;
use App\ValueObject\Pagination;

interface CommentRepositoryInterface
{
    /** @return Comment[] */
    public function getCommentsByPost(PostId $postId, Pagination $pagination): array;

    public function getComment(CommentId $id): ?Comment;

    public function addComment(Comment $comment): CommentId;

    public function removeComment(CommentId $id): bool;
}