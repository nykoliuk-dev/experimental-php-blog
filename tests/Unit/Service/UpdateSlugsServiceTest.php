<?php
declare(strict_types=1);

namespace Service;

use App\Repository\Interface\PostMaintenanceRepositoryInterface;
use App\Service\UpdateSlugsService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\UpdateSlugsService
 */
class UpdateSlugsServiceTest extends TestCase
{
    public function testUpdatingMissingSlugsSuccess(): void
    {
        $repo = $this->createMock(PostMaintenanceRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getPostsWithoutSlug')
            ->willReturn([
                ['id' => '1', 'title' => 'Post One Title'],
                ['id' => '2', 'title' => 'Post Two Title'],
            ]);
        $repo->expects($this->exactly(2))
            ->method('updatePostSlug')
            ->with($this->isType('int'), $this->isType('string'))
            ->willReturn(true);

        $service = new UpdateSlugsService($repo);
        $operationResult = $service->updateMissingSlugs();

        $this->assertSame(2, $operationResult->getSuccessCount());
        $this->assertSame([], $operationResult->getValidationErrors());
        $this->assertSame([], $operationResult->getCriticalErrors());
    }

    public function testUpdatingMissingSlugsWithValidationErrors(): void
    {
        $repo = $this->createMock(PostMaintenanceRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getPostsWithoutSlug')
            ->willReturn([
                ['id' => '1', 'title' => ''],
            ]);

        $service = new UpdateSlugsService($repo);
        $operationResult = $service->updateMissingSlugs();

        $this->assertSame(0, $operationResult->getSuccessCount());
        $this->assertSame(["Skipped post #1: Generated slug is empty for title ''"], $operationResult->getValidationErrors());
    }

    public function testUpdatingMissingSlugsWithException(): void
    {
        $repo = $this->createMock(PostMaintenanceRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getPostsWithoutSlug')
            ->willReturn([
                ['id' => '1', 'title' => 'Post One Title'],
            ]);
        $repo->method('updatePostSlug')
            ->willThrowException(new \RuntimeException("DB error"));

        $service = new UpdateSlugsService($repo);
        $operationResult = $service->updateMissingSlugs();

        $this->assertSame(0, $operationResult->getSuccessCount());
        $this->assertSame([], $operationResult->getValidationErrors());
        $this->assertSame(["Critical error updating slug for post #1 ('Post One Title'): DB error"],
            $operationResult->getCriticalErrors());
    }

    /**
     * @dataProvider slugGenerationDataProvider
     */
    public function testGenerateSlugLogic(string $title, string $expectedSlug): void
    {
        $repo = $this->createMock(PostMaintenanceRepositoryInterface::class);
        $service = new UpdateSlugsService($repo);

        $reflection = new \ReflectionClass(UpdateSlugsService::class);
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
