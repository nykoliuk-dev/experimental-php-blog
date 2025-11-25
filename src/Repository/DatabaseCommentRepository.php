<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Comment;
use App\Model\ValueObject\CommentId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use App\Service\DatabaseService;
use App\ValueObject\Pagination;

class DatabaseCommentRepository implements CommentRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    /**
     *
     * @return Comment[]
     */
    public function getCommentsByPost(PostId $postId, Pagination $pagination): array
    {
        $sql = "SELECT * 
                FROM comments
                WHERE post_id = :post_id
                ORDER BY created_at ASC
                LIMIT {$pagination->limit()} OFFSET {$pagination->offset()}";
        $rows = $this->db->fetchAll($sql, [
            'post_id' => $postId->value(),
        ]);

        return array_map([$this, 'mapRowToComment'], $rows);
    }

    public function getComment(CommentId $id): ?Comment
    {
        $sql = "SELECT * FROM `comments` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id->value()]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToComment($data);
    }

    public function addComment(Comment $comment): CommentId
    {
        $sql = "INSERT INTO `comments` (post_id, user_id, content, created_at) 
        VALUES (:post_id, :user_id, :content, :created_at)";

        $this->db->query($sql, [
            'post_id' => $comment->getPostId()->value(),
            'user_id' => $comment->getUserId()?->value(),
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt(),
        ]);

        return new CommentId($this->db->lastInsertId());
    }

    public function removeComment(CommentId $id): bool
    {
        $sql = "DELETE FROM `comments` WHERE `id` = :id";
        $stmt = $this->db->query($sql, ['id' => $id->value()]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToComment(array $row): Comment
    {
        $id = !empty($row['id']) ? new CommentId((int)$row['id']) : null;
        $userId = !empty($row['user_id']) ? new UserId((int)$row['user_id']) : null;

        return new Comment(
            id: $id,
            postId: new PostId((int)$row['post_id']),
            userId: $userId,
            content: $row['content'],
            createdAt: $row['created_at'],
        );
    }
}