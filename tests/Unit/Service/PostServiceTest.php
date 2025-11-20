<?php

namespace Unit\Service;

use App\Model\Post;
use App\Repository\PostRepositoryInterface;
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
        $userId = 1;
        $title = 'Title';
        $content = 'Content';
        $imageName = 'jpg.jpg';
        $expectedId = 29;
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

        $service = new PostService($postRepositoryMock);
        $id = $service->createPost($userId, $title, $content, $imageName);

        $this->assertSame($expectedId, $id);
    }

    /**
     * @dataProvider slugGenerationDataProvider
     */
    public function testGenerateSlugLogic(string $title, string $expectedSlug): void
    {
        $service = new PostService($this->createMock(PostRepositoryInterface::class));
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
            'Cyrillic title' => ['Привет мир и коты', 'привет-мир-и-коты'],
            'Symbols and spaces' => ['Тайтл! С $ пробелами (123)', 'тайтл-с-пробелами-123'],
            'Leading and trailing spaces' => ['   Testing Trim  ', 'testing-trim'],
            'Only symbols' => ['!!! $$$ %%%', ''],
        ];
    }
}
