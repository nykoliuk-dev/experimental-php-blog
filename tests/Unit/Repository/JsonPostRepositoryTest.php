<?php

namespace Unit\Repository;

use App\Model\Post;
use App\Repository\JsonPostRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repository\JsonPostRepository
 */
class JsonPostRepositoryTest extends TestCase
{
    protected string $tempFile;
    protected JsonPostRepository $repo;

    protected function setUp(): void
    {
        $tempDir = __DIR__ . '/../../../tmp/test';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $this->tempFile = tempnam($tempDir, 'json_test_');
        file_put_contents($this->tempFile, json_encode([]));

        $this->repo = new JsonPostRepository($this->tempFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testAddPostSavesAndReturnsConsistentData(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');

        $expectedId = 0;

        $actualId = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($actualId);

        $this->assertSame($expectedId, $actualId);
        $this->assertSame($post->getDate(), $actualPost->getDate());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
        $this->assertSame($post->getContent(), $actualPost->getContent());
        $this->assertSame($post->getImgName(), $actualPost->getImgName());
    }

    public function testAutoIncrementId(): void
    {
        $post1 = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');
        $post2 = new Post(null, '2025-11-07', 'Title2', 'Content2', 'img.jpg');

        $expectedFirstId = 0;
        $expectedSecondId = 1;

        $firstId = $this->repo->addPost($post1);
        $secondId = $this->repo->addPost($post2);

        $this->assertSame($expectedFirstId, $firstId);
        $this->assertSame($expectedSecondId, $secondId);
    }

    public function testRemovePost(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');

        $id = $this->repo->addPost($post);

        $result = $this->repo->removePost($id);

        $this->assertTrue($result);
        $this->assertFalse($this->repo->removePost($id));
        $this->assertEmpty($this->repo->getPosts());
    }

    public function testGetPosts(): void
    {
        $expectedFirstId = 0;
        $expectedSecondId = 1;

        $posts = [
            $expectedFirstId => new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg'),
            $expectedSecondId => new Post(null, '2025-11-07', 'Title2', 'Content2', 'img.jpg'),
        ];

        $expectedCount = count($posts);

        $firstId = $this->repo->addPost($posts[$expectedFirstId]);
        $secondId = $this->repo->addPost($posts[$expectedSecondId]);

        $actualPosts = $this->repo->getPosts();

        $this->assertCount($expectedCount, $actualPosts);

        foreach ($posts as $id => $post){
            $actualPost = $actualPosts[$id];
            $this->assertSame($post->getDate(), $actualPost->getDate());
            $this->assertSame($post->getTitle(), $actualPost->getTitle());
            $this->assertSame($post->getContent(), $actualPost->getContent());
            $this->assertSame($post->getImgName(), $actualPost->getImgName());
        }
    }

    public function testGetPost(): void
    {
        $post = new Post(null, '2025-11-07', 'Title', 'Content', 'img.jpg');
        $expectedId = 0;

        $actualId = $this->repo->addPost($post);

        $actualPost = $this->repo->getPost($actualId);

        $this->assertSame($expectedId, $actualId);
        $this->assertSame($post->getDate(), $actualPost->getDate());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
        $this->assertSame($post->getContent(), $actualPost->getContent());
        $this->assertSame($post->getImgName(), $actualPost->getImgName());
    }
}
