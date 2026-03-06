<?php
declare(strict_types=1);

namespace Repository;

use App\Repository\PostMaintenanceRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class PostMaintenanceRepositoryTest extends TestCase
{
    protected ?PDO $pdo;
    protected PostMaintenanceRepository $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo->exec("
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                date TEXT NOT NULL,
                title TEXT NOT NULL,
                slug TEXT,
                content TEXT NOT NULL,
                image_name TEXT NOT NULL
            )
        ");

        $dbService = new \App\Service\DatabaseService($this->pdo);
        $this->repo = new PostMaintenanceRepository($dbService);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        unset($this->repo);
    }

    public function testGetPostsWithoutSlug(): void
    {
        $this->pdo->exec("INSERT INTO `posts` (user_id, date, title, slug, content, image_name) 
        VALUES (1, '2025-11-07', 'Post One Title', null, 'Content', 'img.jpg'),
               (1, '2025-11-07', 'Post Two Title', null, 'Content2', 'img2.jpg'),
               (1, '2025-11-07', 'Valid Post', 'has-slug', 'Content3', 'img3.jpg')");

        $posts = $this->repo->getPostsWithoutSlug();

        $this->assertCount(2, $posts);
        $this->assertEquals([
            'id' => '1',
            'title' => 'Post One Title',
        ], $posts[0], 'Первый пост должен иметь только id и title.');
        $this->assertEquals([
            'id' => '2',
            'title' => 'Post Two Title',
        ], $posts[1], 'Второй пост должен иметь только id и title.');
    }


    /**
     * Tests updating the slug for a post by its ID.
     */
    public function testUpdatePostSlug(): void
    {
        $postId = 5;
        $newSlug = 'new-slug';
        $this->pdo->exec("INSERT INTO `posts` (id, user_id, date, title, slug, content, image_name) 
        VALUES ({$postId}, 1, '2025-11-07', 'Old Title', 'old-slug', 'Content', 'img.jpg')");

        $res = $this->repo->updatePostSlug($postId, $newSlug);
        $this->assertTrue($res);

        $stmt = $this->pdo->query("SELECT slug FROM `posts` WHERE id = {$postId}");
        $updatedSlug = $stmt->fetchColumn();

        $this->assertSame($newSlug, $updatedSlug);

        $resFalse = $this->repo->updatePostSlug(100, 'non-existent-slug');
        $this->assertFalse($resFalse);
    }
}
