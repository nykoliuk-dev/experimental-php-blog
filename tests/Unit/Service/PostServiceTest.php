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
}
