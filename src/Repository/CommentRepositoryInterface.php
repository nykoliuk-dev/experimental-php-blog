<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Comment;

interface CommentRepositoryInterface
{
    /** @return Comment[] */
    public function getCommentsByPost(int $postId): array;

    public function getComment(int $id): ?Comment;

    public function addComment(Comment $comment): int;

    public function removeComment(int $id): bool;
}