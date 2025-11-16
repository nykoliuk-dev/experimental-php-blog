<?php

namespace Unit\Service;

use App\DTO\MigrationResult;
use App\Model\Post;
use App\Repository\PostRepositoryInterface;
use App\Service\PostMigrationService;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\callback;

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
                $this->makePost(1),
                $this->makePost(2, 'Title2', 'Content2'),
            ]);
        $this->targetRepo->expects($this->exactly(2))
            ->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $result = $this->migrate();

        $this->assertSame(2, $result->getMigratedCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrors(): void
    {
        $invalidPost = $this->makeInvalidPost(1);

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

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame(["Post №1 is not valid"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrorPostExists(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $this->makePost(1),
            ]);
        $this->targetRepo->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(new Post(1, '2025-11-07', 'Title', 'Content', 'img.jpg'));

        $result = $this->migrate();

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame(["Post №1 already exists"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationAddPostWithException(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                $this->makePost(1),
            ]);
        $this->targetRepo->method('addPost')->willThrowException(new \RuntimeException("DB error"));
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $result = $this->migrate();

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame(['Failed to migrate post №1: DB error'], $result->getCriticalErrors());
    }

    private function makePost(
        int $id,
        string $title = 'Title',
        string $content = 'Content'
    ): Post {
        return new Post($id, '2025-11-07', $title, $content, 'img.jpg');
    }

    private function makeInvalidPost(int $id): Post
    {
        $post = $this->createMock(Post::class);
        $post->method('getId')->willReturn($id);
        $post->method('getTitle')->willReturn('');
        $post->method('getContent')->willReturn('');
        return $post;
    }

    private function migrate(): MigrationResult
    {
        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);
        return $service->migrate();
    }
}