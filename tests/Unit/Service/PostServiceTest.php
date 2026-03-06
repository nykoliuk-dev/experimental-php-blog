<?php
declare(strict_types=1);

namespace Unit\Service;

use App\Model\Post;
use App\Model\ValueObject\CategoryId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\TagId;
use App\Model\ValueObject\UserId;
use App\Repository\Interface\PostRepositoryInterface;
use App\Service\Interface\PostCategoryServiceInterface;
use App\Service\Interface\PostTagServiceInterface;
use App\Service\Interface\TransactionManagerInterface;
use App\Service\PostService;
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

        $transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
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
            $this->createMock(PostCategoryServiceInterface::class),
            $this->createMock(PostTagServiceInterface::class),
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

        $postCategoryServiceMock = $this->createMock(PostCategoryServiceInterface::class);
        $postCategoryServiceMock->expects($this->once())
            ->method('setPostCategories')
            ->with(
                $this->isInstanceOf(PostId::class),
                $this->callback(function ($categoryIds) use ($categories)
                {
                    if (count($categoryIds) !== count($categories)) {
                        return false;
                    }

                    foreach ($categoryIds as $categoryId) {
                        if (! $categoryId instanceof CategoryId) {
                            return false;
                        }
                    }

                    $values = array_map(fn(CategoryId $id) => $id->value(), $categoryIds);

                    return $values === $categories;
                })
            );

        $transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
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
            $this->createMock(PostTagServiceInterface::class),
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

        $postCategoryServiceMock = $this->createMock(PostCategoryServiceInterface::class);

        $postTagServiceMock = $this->createMock(PostTagServiceInterface::class);
        $postTagServiceMock->expects($this->once())
            ->method('setPostTags')
            ->with(
                $this->isInstanceOf(PostId::class),
                $this->callback(function ($tagIds) use ($tags)
                {
                    if (count($tagIds) !== count($tags)) {
                        return false;
                    }

                    foreach ($tagIds as $tagId) {
                        if (! $tagId instanceof TagId) {
                            return false;
                        }
                    }

                    $values = array_map(fn(TagId $id) => $id->value(), $tagIds);

                    return $values === $tags;
                })
            );

        $transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
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
            $this->createMock(PostCategoryServiceInterface::class),
            $this->createMock(PostTagServiceInterface::class),
            $this->createMock(TransactionManagerInterface::class)
        );

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('generateSlug');
        /** @noinspection PhpExpressionResultUnusedInspection */
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
