<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;
use App\Service\DatabaseService;

class DatabasePostRepository implements PostRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    public function getPosts(): array
    {
        $sql = "SELECT * FROM `posts` ORDER BY `date` DESC";
        $rows = $this->db->fetchAll($sql);

        return array_map([$this, 'mapRowToPost'], $rows);
    }

    public function getPost(int $id): ?Post
    {
        $sql = "SELECT * FROM `posts` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToPost($data);
    }

    public function addPost(Post $post): int
    {
        $sql = "INSERT INTO `posts` (date, title, content, image_name) 
        VALUES (:date, :title, :content, :image_name)";
        $this->db->query($sql, [
            'date' => $post->getDate(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'image_name' => $post->getImgName(),
        ]);

        return $this->db->lastInsertId();
    }

    public function removePost(int $id): bool
    {
        $sql = "DELETE FROM `posts` WHERE `id` = (:id)";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToPost(array $row): Post
    {
        return new Post(
            (int)$row['id'],
            $row['date'],
            $row['title'],
            $row['content'],
            $row['image_name']
        );
    }
}