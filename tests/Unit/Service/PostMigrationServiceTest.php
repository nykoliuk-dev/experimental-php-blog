<?php

namespace Unit\Service;

use App\DTO\OperationResult;
use App\Model\Post;
use App\Repository\Interfaces\PostRepositoryInterface;
use App\Service\PostMigrationService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\PostMigrationService
 */
class PostMigrationServiceTest extends TestCase
{
    protected PostRepositoryInterface $sourceRepo;
    protected PostRepositoryInterface $targetRepo;

    protected function setUp(): void
    {
        $this->sourceRepo = $this->createMock(PostRepositoryInterface::class);
        $this->targetRepo = $this->createMock(PostRepositoryInterface::class);
    }
    public function testMigrationSuccess(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $this->makePost(id: 1),
                $this->makePost(id: 2, title: 'Title2', slug: 'test-slug2', content: 'Content2'),
            ]);
        $this->targetRepo->expects($this->exactly(2))
            ->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $result = $this->migrate();

        $this->assertSame(2, $result->getSuccessCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrors(): void
    {
        $invalidPost = $this->makeInvalidPost(id: 1);

        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $invalidPost,
            ]);
        $this->targetRepo->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $result = $this->migrate();

        $this->assertSame(0, $result->getSuccessCount());
        $this->assertSame(["Post №1 is not valid"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrorPostExists(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $this->makePost(id: 1),
            ]);
        $this->targetRepo->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn($this->makePost(id: 1));

        $result = $this->migrate();

        $this->assertSame(0, $result->getSuccessCount());
        $this->assertSame(["Post №1 already exists"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationAddPostWithException(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $this->makePost(id: 1),
            ]);
        $this->targetRepo->method('addPost')->willThrowException(new \RuntimeException("DB error"));
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $result = $this->migrate();

        $this->assertSame(0, $result->getSuccessCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame(['Failed to migrate post №1: DB error'], $result->getCriticalErrors());
    }

    private function makePost(
        int $id,
        string $title = 'Title',
        string $slug = 'test-slug',
        string $content = 'Content'
    ): Post {
        return new Post(
            id: $id,
            userId: null,
            date: '2025-11-07',
            title: $title,
            slug: $slug,
            content: $content,
            imageName: 'img.jpg'
        );
    }

    private function makeInvalidPost(int $id): Post
    {
        $post = $this->createMock(Post::class);
        $post->method('getId')->willReturn($id);
        $post->method('getTitle')->willReturn('');
        $post->method('getContent')->willReturn('');
        return $post;
    }

    private function migrate(): OperationResult
    {
        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);
        return $service->migrate();
    }
}