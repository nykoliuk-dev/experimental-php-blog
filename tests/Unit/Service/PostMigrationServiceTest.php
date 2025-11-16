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
                new Post(1, '2025-11-07', 'Title', 'Content', 'img.jpg'),
                new Post(2, '2025-11-07', 'Title2', 'Content2', 'img2.jpg'),
            ]);
        $this->targetRepo->expects($this->exactly(2))
            ->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);

        $result = $service->migrate();

        $this->assertSame(2, $result->getMigratedCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrors(): void
    {
        $invalidPost = $this->createMock(Post::class);
        $invalidPost->method('getId')->willReturn(1);
        $invalidPost->method('getTitle')->willReturn('');
        $invalidPost->method('getContent')->willReturn('Content');

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

        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);

        $result = $service->migrate();

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame(["Post №1 is not valid"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationWithValidationErrorPostExists(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                new Post(1, '2025-11-07', 'Title', 'Content', 'img.jpg'),
            ]);
        $this->targetRepo->method('addPost')
            ->with($this->isInstanceOf(Post::class))
            ->willReturnCallback(fn(Post $p) => $p->getId());
        $this->targetRepo->method('getPost')
            ->willReturn(new Post(1, '2025-11-07', 'Title', 'Content', 'img.jpg'));

        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);

        $result = $service->migrate();

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame(["Post №1 already exists"], $result->getValidationErrors());
        $this->assertSame([], $result->getCriticalErrors());
    }

    public function testMigrationAddPostWithException(): void
    {
        $this->sourceRepo->expects($this->once())
            ->method('getPosts')
            ->willReturn([
                new Post(1, '2025-11-07', 'Title', 'Content', 'img.jpg'),
            ]);
        $this->targetRepo->method('addPost')->willThrowException(new \RuntimeException("DB error"));
        $this->targetRepo->method('getPost')
            ->willReturn(null);

        $service = new PostMigrationService($this->sourceRepo, $this->targetRepo);

        $result = $service->migrate();

        $this->assertSame(0, $result->getMigratedCount());
        $this->assertSame([], $result->getValidationErrors());
        $this->assertSame(['Failed to migrate post №1: DB error'], $result->getCriticalErrors());
    }
}