<?php

namespace Unit\Service;

use App\Model\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\PostService;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    public function testCreatePost(): void
    {
        $title = 'Test Title';
        $content = 'Test Content';
        $imageName = 'test.jpg';
        $expectedId = 42;

        $postRepositoryMock = $this->createMock(PostRepositoryInterface::class);
        $postRepositoryMock->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturn($expectedId);

        $service = new PostService($postRepositoryMock);
        $id = $service->createPost($title, $content, $imageName);

        $this->assertSame($expectedId, $id);
    }
}
