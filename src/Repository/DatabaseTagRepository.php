<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Tag;
use App\Service\DatabaseService;

class DatabaseTagRepository implements TagRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    /** @return Tag[] */
    public function getTags(): array
    {
        $sql = "SELECT * FROM `tags` ORDER BY `name`";
        $rows = $this->db->fetchAll($sql);

        return array_map([$this, 'mapRowToTag'], $rows);
    }

    public function getTag(int $id): ?Tag
    {
        $sql = "SELECT * FROM `tags` WHERE id=:id";
        $data = $this->db->fetchOne($sql, ['id' => $id]);
        if (!$data) {
            return null;
        }

        return $this->mapRowToTag($data);
    }

    /** @return Tag[] */
    public function getTagsByPost(int $postId): array
    {
        $sql = "SELECT * FROM `tags` WHERE id IN(
                    SELECT tag_id FROM `post_tag` WHERE post_id=:id
        )";
        $rows = $this->db->fetchAll($sql, ['id' => $postId]);

        return array_map([$this, 'mapRowToTag'], $rows);
    }

    public function addTag(Tag $tag): int
    {
        $sql = "INSERT INTO `tags` (name, slug) 
        VALUES (:name, :slug)";

        $this->db->query($sql, [
            'name' => $tag->getName(),
            'slug' => $tag->getSlug(),
        ]);

        return $this->db->lastInsertId();
    }

    public function removeTag(int $id): bool
    {
        $sql = "DELETE FROM `tags` WHERE `id` = :id";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function mapRowToTag(array $row): Tag
    {
        return new Tag(
            id: (int)$row['id'],
            name: $row['name'],
            slug: $row['slug'],
        );
    }
}