<?php

namespace Unit\Service;

use App\Model\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\PostService;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    private const DATETIME_PATTERN = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
    public function testCreatePost(): void
    {
        $title = 'Title';
        $content = 'Content';
        $imageName = 'jpg.jpg';
        $expectedId = 29;

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->expects($this->once())
            ->method('addPost')
            ->with($this->callback(function (Post $post) use ($title, $content, $imageName)
            {
                $this->assertNull($post->getId());
                $this->assertSame($title, $post->getTitle());
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
        $id = $service->createPost($title, $content, $imageName);

        $this->assertSame($expectedId, $id);
    }
}
