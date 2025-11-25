<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Comment;
use App\Service\DatabaseService;

class DatabaseCommentRepository implements CommentRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    /** @return Comment[] */
    public function getCommentsByPost(int $postId, int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT * 
                FROM comments
                WHERE post_id = :post_id
                ORDER BY created_at ASC
                LIMIT $limit OFFSET $offset";
        $rows = $this->db->fetchAll($sql, ['post_id' => $postId]);

        return array_map([$this, 'mapRowToComment'], $rows);
    }

    public function getComment(int $id): ?Comment
    {
        $sql = "SELECT * FROM `comments` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToComment($data);
    }

    public function addComment(Comment $comment): int
    {
        $sql = "INSERT INTO `comments` (post_id, user_id, content, created_at) 
        VALUES (:post_id, :user_id, :content, :created_at)";

        $this->db->query($sql, [
            'post_id' => $comment->getPostId(),
            'user_id' => $comment->getUserId(),
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt(),
        ]);

        return $this->db->lastInsertId();
    }

    public function removeComment(int $id): bool
    {
        $sql = "DELETE FROM `comments` WHERE `id` = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToComment(array $row): Comment
    {
        $userId = !empty($row['user_id']) ? (int)$row['user_id'] : null;

        return new Comment(
            id: (int)$row['id'],
            postId: (int)$row['post_id'],
            userId: $userId,
            content: $row['content'],
            createdAt: $row['created_at'],
        );
    }
}