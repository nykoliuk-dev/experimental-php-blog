<?php
declare(strict_types=1);

namespace Unit\Service;

use App\Model\Post;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\PostCategoryService;
use App\Service\PostService;
use App\Service\PostTagService;
use App\Service\TransactionManager;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
* @covers \App\Service\PostService
*/
class PostServiceTest extends TestCase
{
    private const DATETIME_PATTERN = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
    public function testCreatePost(): void
    {
        //Arrange
        $userId = new UserId(1);
        $title = 'Title';
        $content = 'Content';
        $imageName = 'jpg.jpg';
        $expectedId = new PostId(29);
        $expectedSlug = 'title';

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('addPost')
            ->with($this->callback(function (Post $post) use ($userId, $title, $expectedSlug, $content, $imageName)
            {
                $this->assertNull($post->getId());
                $this->assertSame($userId, $post->getUserId());
                $this->assertSame($title, $post->getTitle());
                $this->assertSame($expectedSlug, $post->getSlug());
                $this->assertSame($content, $post->getContent());
                $this->assertSame($imageName, $post->getImgName());

                $this->assertMatchesRegularExpression(
                    self::DATETIME_PATTERN,
                    $post->getDate()
                );

                return true;
            }))
            ->willReturn($expectedId);

        $postCategoryServiceMock = $this->createMock(PostCategoryService::class);
        $postTagServiceMock = $this->createMock(PostTagService::class);

        $transactionManagerMock = $this->createMock(TransactionManager::class);
        $transactionManagerMock->expects($this->once())
            ->method('wrap')
            ->willReturnCallback(function (callable $callback)
            {
                $result = $callback();
                return $result;
            });

        //Act
        $service = new PostService(
            $postRepositoryMock,
            $postCategoryServiceMock,
            $postTagServiceMock,
            $transactionManagerMock
        );
        $id = $service->createPost($userId, $title, $content, $imageName);

        //Assert
        $this->assertSame($expectedId, $id);
    }

    public function testCreatePost_WithCategories(): void
    {
        //Arrange
        $userId = new UserId(1);
        $title = 'Title';
        $content = 'Content';
        $imageName = 'jpg.jpg';
        $expectedId = new PostId(29);
        $expectedSlug = 'title';
        $categories = [10, 20];

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('addPost')
            ->willReturn($expectedId);

        $postCategoryServiceMock = $this->createMock(PostCategoryService::class);
        $postCategoryServiceMock->expects($this->once())
            ->method('setPostCategories')
            ->with(
                $this->callback(function (PostId $postId) use ($expectedId)
                {
                    $this->assertSame($expectedId, $postId);
                    return true;
                }),
                $this->callback(function ($categoryIds) use ($categories)
                {
                    $this->assertEquals($categories, [$categoryIds[0]->value(), $categoryIds[1]->value()]);
                    return true;
                })
            );

        $postTagServiceMock = $this->createMock(PostTagService::class);

        $transactionManagerMock = $this->createMock(TransactionManager::class);
        $transactionManagerMock->expects($this->once())
            ->method('wrap')
            ->willReturnCallback(function (callable $callback)
            {
                $result = $callback();
                return $result;
            });

        //Act
        $service = new PostService(
            $postRepositoryMock,
            $postCategoryServiceMock,
            $postTagServiceMock,
            $transactionManagerMock
        );
        $id = $service->createPost(
            userId: $userId,
            title: $title,
            content: $content,
            imageName: $imageName,
            categories: $categories,
        );

        //Assert
        $this->assertSame($expectedId, $id);
    }

    public function testCreatePost_WithTags(): void
    {
        //Arrange
        $userId = new UserId(1);
        $title = 'Title';
        $content = 'Content';
        $imageName = 'jpg.jpg';
        $expectedId = new PostId(29);
        $expectedSlug = 'title';
        $tags = [10, 20];

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('addPost')
            ->willReturn($expectedId);

        $postCategoryServiceMock = $this->createMock(PostCategoryService::class);

        $postTagServiceMock = $this->createMock(PostTagService::class);
        $postTagServiceMock->expects($this->once())
            ->method('setPostTags')
            ->with(
                $this->isInstanceOf(PostId::class),
                $this->callback(function ($tagIds)
                {
                    if (empty($tagIds)) {
                        return false;
                    }

                    foreach ($tagIds as $tagId) {
                        if (! $tagId instanceof TagId) {
                            return false;
                        }
                    }

                    return true;
                })
            );

        $transactionManagerMock = $this->createMock(TransactionManager::class);
        $transactionManagerMock->expects($this->once())
            ->method('wrap')
            ->willReturnCallback(function (callable $callback)
            {
                $result = $callback();
                return $result;
            });

        //Act
        $service = new PostService(
            $postRepositoryMock,
            $postCategoryServiceMock,
            $postTagServiceMock,
            $transactionManagerMock
        );
        $id = $service->createPost(
            userId: $userId,
            title: $title,
            content: $content,
            imageName: $imageName,
            tags: $tags,
        );

        //Assert
        $this->assertSame($expectedId, $id);
    }

    /**
     * @dataProvider slugGenerationDataProvider
     */
    public function testGenerateSlugLogic(string $title, string $expectedSlug): void
    {
        $service = new PostService(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(PostCategoryService::class),
            $this->createMock(PostTagService::class),
            $this->createMock(TransactionManager::class)
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('generateSlug');
        $method->setAccessible(true);

        $generatedSlug = $method->invoke($service, $title);

        $this->assertSame($expectedSlug, $generatedSlug);
    }

    public function slugGenerationDataProvider(): array
    {
        return [
            'Simple title' => ['My Awesome Title', 'my-awesome-title'],
            'Cyrillic title' => ['Привет мир и коты', 'privet-mir-i-koty'],
            'Symbols and spaces' => ['Тайтл! С $ пробелами (123)', 'taytl-s-probelami-123'],
            'Leading and trailing spaces' => ['   Testing Trim  ', 'testing-trim'],
            'Only symbols' => ['!!! $$$ %%%', ''],
        ];
    }
}
