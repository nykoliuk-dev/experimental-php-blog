<?php

namespace Unit\Repository;

use App\Model\Post;
use App\Repository\DatabasePostRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class DatabasePostRepositoryTest extends TestCase
{
    protected ?PDO $pdo;
    protected DatabasePostRepository $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date TEXT NOT NULL,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                image_name TEXT NOT NULL
            )
        ");

        $dbService = new \App\Service\DatabaseService($this->pdo);
        $this->repo = new \App\Repository\DatabasePostRepository($dbService);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        unset($this->repo);
    }

    public function testAddPostSavesAndReturnsConsistentData(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');

        $id = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($id);

        $this->assertNotNull($id);
        $this->assertIsInt($id);

        $this->assertSame($post->getDate(), $actualPost->getDate());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
        $this->assertSame($post->getContent(), $actualPost->getContent());
        $this->assertSame($post->getImgName(), $actualPost->getImgName());

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        $this->assertSame(1, (int)$stmt->fetchColumn());
    }

    public function testRemovePost(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');

        $id = $this->repo->addPost($post);

        $result = $this->repo->removePost($id);

        $this->assertTrue($result);
        $this->assertFalse($this->repo->removePost($id));
        $this->assertEmpty($this->repo->getPosts());

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        $this->assertSame(0, (int)$stmt->fetchColumn());
    }

    public function testGetPosts(): void
    {
        $post1 = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');
        $post2 = new Post(null, '2025-11-07', 'Title2', 'Content2', 'img.jpg');

        $firstId = $this->repo->addPost($post1);
        $secondId = $this->repo->addPost($post2);

        $actualPosts = $this->repo->getPosts();

        $this->assertCount(2, $actualPosts);

        $this->assertSame($post1->getDate(), $actualPosts[$firstId]->getDate());
        $this->assertSame($post1->getTitle(), $actualPosts[$firstId]->getTitle());
        $this->assertSame($post1->getContent(), $actualPosts[$firstId]->getContent());
        $this->assertSame($post1->getImgName(), $actualPosts[$firstId]->getImgName());

        $this->assertSame($post2->getDate(), $actualPosts[$secondId]->getDate());
        $this->assertSame($post2->getTitle(), $actualPosts[$secondId]->getTitle());
        $this->assertSame($post2->getContent(), $actualPosts[$secondId]->getContent());
        $this->assertSame($post2->getImgName(), $actualPosts[$secondId]->getImgName());
    }

    public function testGetPost(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');
        $expectedId = 1;

        $actualId = $this->repo->addPost($post);

        $actualPost = $this->repo->getPost($actualId);

        $this->assertSame($expectedId, $actualId);
        $this->assertSame($post->getDate(), $actualPost->getDate());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
        $this->assertSame($post->getContent(), $actualPost->getContent());
        $this->assertSame($post->getImgName(), $actualPost->getImgName());
    }
}
