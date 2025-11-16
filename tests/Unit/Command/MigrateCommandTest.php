<?php

namespace Unit\Command;

use App\Command\MigrateCommand;
use App\DTO\MigrationResult;
use App\Service\PostMigrationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Command\MigrateCommand
 */
class MigrateCommandTest extends TestCase
{
    public function testMigrationSuccess(): void
    {
        $service = $this->createMock(PostMigrationService::class);
        $service->expects($this->once())
            ->method('migrate')
            ->willReturn(new MigrationResult(2, [], []));

        $application = new Application();
        $application->add(new MigrateCommand($service));

        $command = $application->find('app:migrate:posts');
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('2 post(s) successfully added', $tester->getDisplay());
    }

    public function testMigrationFailedHasCriticalErrors(): void
    {
        $service = $this->createMock(PostMigrationService::class);
        $service->expects($this->once())
            ->method('migrate')
            ->willReturn(new MigrationResult(2, ["Failed to migrate post №1"], []));

        $application = new Application();
        $application->add(new MigrateCommand($service));

        $command = $application->find('app:migrate:posts');
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('Failed to migrate post №1', $tester->getDisplay());
    }
}
