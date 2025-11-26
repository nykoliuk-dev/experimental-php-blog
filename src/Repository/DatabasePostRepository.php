<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
use App\Repository\Interfaces\PostRepositoryInterface;
use App\Service\DatabaseService;

class DatabasePostRepository implements PostRepositoryInterface
{
    public function __construct(private DatabaseService $db)
    {
    }

    public function getPosts(): array
    {
        $sql = "SELECT * FROM `posts` ORDER BY `date` DESC";
        $rows = $this->db->fetchAll($sql);

        return array_map([$this, 'mapRowToPost'], $rows);
    }

    public function getPost(PostId $id): ?Post
    {
        $sql = "SELECT * FROM `posts` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id->value()]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToPost($data);
    }

    public function addPost(Post $post): PostId
    {
        $sql = "INSERT INTO `posts` (user_id, date, title, slug, content, image_name) 
        VALUES (:user_id, :date, :title, :slug, :content, :image_name)";

        $this->db->query($sql, [
            'user_id' => $post->getUserId()?->value(),
            'date' => $post->getDate(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'image_name' => $post->getImgName(),
        ]);

        return new PostId($this->db->lastInsertId());
    }

    public function removePost(PostId $id): bool
    {
        $sql = "DELETE FROM `posts` WHERE `id` = (:id)";
        $stmt = $this->db->query($sql, ['id' => $id->value()]);
        return $stmt->rowCount() > 0;
    }

    public function clearPostTags(PostId $postId): bool
    {
        $sqlDelete = "DELETE FROM `post_tag` WHERE `post_id` = (:post_id)";
        $stmt = $this->db->query($sqlDelete, ['post_id' => $postId->value()]);
        return $stmt->rowCount() > 0;
    }

    public function clearPostCategories(PostId $postId): bool
    {
        $sqlDelete = "DELETE FROM `category_post` WHERE `post_id` = (:post_id)";
        $stmt = $this->db->query($sqlDelete, ['post_id' => $postId->value()]);
        return $stmt->rowCount() > 0;
    }

    /** @param TagId[] $tagIds */
    public function addPostTags(PostId $postId, array $tagIds): void
    {
        if (empty($tagIds)) {
            return;
        }

        $values = [];
        $params = [];

        foreach ($tagIds as $i => $tagId) {
            $values[] = "(:post_id_$i, :tag_id_$i)";
            $params["post_id_$i"] = $postId->value();
            $params["tag_id_$i"]  = $tagId->value();
        }

        $sql = "INSERT INTO `post_tag` (post_id, tag_id) VALUES " . implode(', ', $values);

        $this->db->query($sql, $params);
    }

    /** @param CategoryId[] $categoryIds */
    public function addPostCategories(PostId $postId, array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $values = [];
        $params = [];

        foreach ($categoryIds as $i => $categoryId) {
            $values[] = "(:category_id_$i, :post_id_$i)";
            $params["category_id_$i"]  = $categoryId->value();
            $params["post_id_$i"] = $postId->value();
        }

        $sql = "INSERT INTO `category_post` (category_id, post_id) VALUES " . implode(', ', $values);

        $this->db->query($sql, $params);
    }

    private function mapRowToPost(array $row): Post
    {
        $userId = !empty($row['user_id']) ? new UserId((int)$row['user_id']) : null;

        return new Post(
            id: new PostId((int)$row['id']),
            userId: $userId,
            date: $row['date'],
            title: $row['title'],
            slug: $row['slug'],
            content: $row['content'],
            imageName: $row['image_name']
        );
    }
}