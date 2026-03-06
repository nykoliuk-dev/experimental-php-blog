<?php
declare(strict_types=1);

namespace Unit\Repository;

use App\Model\Post;
use App\Repository\DatabasePostRepository;
use Tests\Factory\PostFactory;
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
                user_id INTEGER,
                date TEXT NOT NULL,
                title TEXT NOT NULL,
                slug TEXT NOT NULL,
                content TEXT NOT NULL,
                image_name TEXT NOT NULL
            )
        ");

        $dbService = new \App\Service\DatabaseService($this->pdo);
        $this->repo = new DatabasePostRepository($dbService);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        unset($this->repo);
    }

    public function testAddPostSavesAndReturnsConsistentData(): void
    {
        $post = PostFactory::create();

        $id = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($id);

        $this->assertNotNull($id);
        $this->assertIsInt($id);

        $this->assertPostsEqual($post, $actualPost);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        $this->assertSame(1, (int)$stmt->fetchColumn());
    }

    public function testRemovePost(): void
    {
        $post = PostFactory::create();

        $id = $this->repo->addPost($post);

        $result = $this->repo->removePost($id);

        $this->assertTrue($result);
        $this->assertFalse($this->repo->removePost($id));
        $this->assertEmpty($this->repo->getPosts());

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        $this->assertSame(0, (int)$stmt->fetchColumn());
    }

    public function testGetPostsReturnsAllSavedPosts(): void
    {
        $post1 = PostFactory::create();
        $post2 = PostFactory::create(title: 'Title2', slug: 'title2', content: 'Content2');

        $firstId = $this->repo->addPost($post1);
        $secondId = $this->repo->addPost($post2);

        $actualPosts = $this->repo->getPosts();

        $this->assertCount(2, $actualPosts);

        $this->assertPostsEqual($post1, $actualPosts[0]);
        $this->assertPostsEqual($post2, $actualPosts[1]);
    }

    public function testGetPostReturnsExactPostById(): void
    {
        $post = PostFactory::create();
        $expectedId = 1;

        $actualId = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($actualId);

        $this->assertSame($expectedId, $actualId);
        $this->assertPostsEqual($post, $actualPost);
    }

    public function testGetPostTags(): void
    {
        $this->pdo->exec("
            CREATE TABLE post_tag (
                `post_id` INTEGER UNSIGNED NOT NULL,
                `tag_id` INTEGER UNSIGNED NOT NULL
            )
        ");

        $this->repo->setPostTags(12, [3, 5]);

        $res = $this->repo->getPostTags(12);

        var_dump($res);
    }

    private function assertPostsEqual(Post $expected, Post $actual): void
    {
        $this->assertSame($expected->getUserId(), $actual->getUserId());
        $this->assertSame($expected->getDate(), $actual->getDate());
        $this->assertSame($expected->getTitle(), $actual->getTitle());
        $this->assertSame($expected->getSlug(), $actual->getSlug());
        $this->assertSame($expected->getContent(), $actual->getContent());
        $this->assertSame($expected->getImgName(), $actual->getImgName());
    }
}