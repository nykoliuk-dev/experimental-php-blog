<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Post;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
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
            'user_id' => $post->getUserId()->value(),
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

    /** @param TagId[] $tagIds */
    public function setPostTags(PostId $postId, array $tagIds): void
    {
        $this->db->beginTransaction();

        try {
            $sqlDelete = "DELETE FROM `post_tag` WHERE `post_id` = (:post_id)";
            $this->db->query($sqlDelete, ['post_id' => $postId->value()]);

            foreach ($tagIds as $tagId) {
                $sql = "INSERT INTO `post_tag` (post_id, tag_id) VALUES (:post_id, :tag_id)";

                $this->db->query($sql, [
                    'post_id' => $postId->value(),
                    'tag_id' => $tagId->value(),
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** @param CategoryId[] $categoryIds */
    public function setPostCategories(PostId $postId, array $categoryIds): void
    {
        $this->db->beginTransaction();

        try {
            $sqlDelete = "DELETE FROM `category_post` WHERE `post_id` = (:post_id)";
            $this->db->query($sqlDelete, ['post_id' => $postId->value()]);

            foreach ($categoryIds as $categoryId) {
                $sql = "INSERT INTO `category_post` (category_id, post_id) VALUES (:category_id, :post_id)";

                $this->db->query($sql, [
                    'category_id' => $categoryId->value(),
                    'post_id' => $postId->value(),
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

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