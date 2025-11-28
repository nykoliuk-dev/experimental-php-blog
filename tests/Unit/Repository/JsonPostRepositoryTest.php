<?php
declare(strict_types=1);

namespace Unit\Repository;

use App\Model\Post;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
use App\Repository\JsonPostRepository;
use PHPUnit\Framework\TestCase;
use Tests\Factory\PostFactory;

/**
 * @covers \App\Repository\JsonPostRepository
 */
class JsonPostRepositoryTest extends TestCase
{
    protected string $tempFile;
    protected JsonPostRepository $repo;
    protected string $tagsStorage;
    protected string $categoriesStorage;

    protected function setUp(): void
    {
        $tempDir = __DIR__ . '/../../../tmp/test';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $this->tempFile = tempnam($tempDir, 'json_test_posts_');
        file_put_contents($this->tempFile, json_encode([]));

        $this->repo = new JsonPostRepository($this->tempFile);

        $dir = dirname($this->tempFile);
        $this->tagsStorage = $dir . '/post_tags.json';
        $this->categoriesStorage = $dir . '/post_categories.json';

        file_put_contents($this->tagsStorage, json_encode([]));
        file_put_contents($this->categoriesStorage, json_encode([]));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        if (file_exists($this->tagsStorage)) {
            unlink($this->tagsStorage);
        }
        if (file_exists($this->categoriesStorage)) {
            unlink($this->categoriesStorage);
        }
    }

    public function testAddPostSavesAndReturnsConsistentData(): void
    {
        $post = PostFactory::create(
            userId: new UserId(1),
        );

        $expectedId = 1;

        $postId = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($postId);

        $this->assertInstanceOf(PostId::class, $postId);
        $this->assertSame($expectedId, $postId->value());
        $this->assertSame($post->getDate(), $actualPost->getDate());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
        $this->assertSame($post->getSlug(), $actualPost->getSlug());
        $this->assertSame($post->getContent(), $actualPost->getContent());
        $this->assertSame($post->getImgName(), $actualPost->getImgName());
        $this->assertSame($post->getUserId()->value(), $actualPost->getUserId()->value());
    }

    public function testAutoIncrementId(): void
    {
        $post1 = PostFactory::create(
            title: 'Title 1',
            content: 'Content 1'
        );
        $post2 = PostFactory::create(
            title: 'Title 2',
            content: 'Content 2'
        );

        $expectedFirstId = 1;
        $expectedSecondId = 2;

        $firstPostId = $this->repo->addPost($post1);
        $secondPostId = $this->repo->addPost($post2);

        $this->assertSame($expectedFirstId, $firstPostId->value());
        $this->assertSame($expectedSecondId, $secondPostId->value());
    }

    public function testRemovePost(): void
    {
        $post = PostFactory::create();
        $tagIds = [new TagId(10), new TagId(20)];
        $categoryIds = [new CategoryId(1), new CategoryId(2)];

        $postId = $this->repo->addPost($post);
        $this->repo->addPostTags($postId, $tagIds);
        $this->repo->addPostCategories($postId, $categoryIds);

        $result = $this->repo->removePost($postId);

        $this->assertTrue($result);
        $this->assertFalse($this->repo->removePost($postId));
        $this->assertEmpty($this->repo->getPosts());

        $this->assertFalse($this->repo->clearPostTags($postId));
        $this->assertFalse($this->repo->clearPostCategories($postId));
    }

    public function testGetPosts(): void
    {
        $postsData = [
            1 => ['title' => 'Title 1', 'slug' => 'slug-1', 'content' => 'Content 1'],
            2 => ['title' => 'Title 2', 'slug' => 'slug-2', 'content' => 'Content 2'],
        ];

        $allPosts = [];

        foreach ($postsData as $id => $data) {
            $post = new Post(
                id: null,
                userId: new UserId(99),
                date: '2025-11-07',
                title: $data['title'],
                slug: $data['slug'],
                content: $data['content'],
                imageName: 'img.jpg'
            );
            $this->repo->addPost($post);
            $allPosts[$id] = $post;
        }

        $actualPosts = $this->repo->getPosts();

        $this->assertCount(count($allPosts), $actualPosts);

        foreach ($allPosts as $id => $expectedPost){
            /** @var Post $actualPost */
            $actualPost = $actualPosts[$id];

            $this->assertSame($id, $actualPost->getId()->value());
            $this->assertSame($expectedPost->getUserId()->value(), $actualPost->getUserId()->value());
        }
    }

    public function testGetPost(): void
    {
        $post = PostFactory::create(
            userId: new UserId(42),
        );

        $expectedId = 1;

        $actualIdObject = $this->repo->addPost($post);
        $actualPost = $this->repo->getPost($actualIdObject);

        $this->assertSame($expectedId, $actualIdObject->value());
        $this->assertSame($post->getUserId()->value(), $actualPost->getUserId()->value());
        $this->assertSame($post->getTitle(), $actualPost->getTitle());
    }

    public function testGetPostReturnsNullWhenNotFound(): void
    {
        $nonExistentId = new PostId(999);
        $this->assertNull($this->repo->getPost($nonExistentId));
    }

    public function testAddAndClearPostTags(): void
    {
        $post = PostFactory::create();
        $postId = $this->repo->addPost($post);

        $tagIds = [new TagId(10), new TagId(20)];

        $this->repo->addPostTags($postId, $tagIds);

        $relations = $this->loadJsonData($this->tagsStorage);
        $this->assertArrayHasKey($postId->value(), $relations);
        $this->assertSame([10, 20], $relations[$postId->value()]);

        $result = $this->repo->clearPostTags($postId);
        $this->assertTrue($result);

        $relationsAfterClear = $this->loadJsonData($this->tagsStorage);
        $this->assertArrayNotHasKey($postId->value(), $relationsAfterClear);
        $this->assertFalse($this->repo->clearPostTags($postId));
    }

    public function testAddAndClearPostCategories(): void
    {
        $post = PostFactory::create();
        $postId = $this->repo->addPost($post);

        $categoryIds = [new CategoryId(1), new CategoryId(2)];

        $this->repo->addPostCategories($postId, $categoryIds);

        $relations = $this->loadJsonData($this->categoriesStorage);
        $this->assertArrayHasKey($postId->value(), $relations);
        $this->assertSame([1, 2], $relations[$postId->value()]);

        $result = $this->repo->clearPostCategories($postId);
        $this->assertTrue($result);

        $relationsAfterClear = $this->loadJsonData($this->categoriesStorage);
        $this->assertArrayNotHasKey($postId->value(), $relationsAfterClear);

        $this->assertFalse($this->repo->clearPostCategories($postId));
    }

    /**
     * Helper method to load JSON data directly (for relation file testing)
     */
    private function loadJsonData(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }
}
