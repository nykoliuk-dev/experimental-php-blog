<?php
declare(strict_types=1);

namespace Command;

use App\Command\UpdateSlugsCommand;
use App\DTO\OperationResult;
use App\Service\UpdateSlugsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Command\UpdateSlugsCommand
 */
class UpdateSlugsCommandTest extends TestCase
{
    public function testUpdateSlugsSuccess(): void
    {
        $service = $this->createMock(UpdateSlugsService::class);
        $service->expects($this->once())
            ->method('updateMissingSlugs')
            ->willReturn(new OperationResult(2, [], []));

        $application = new Application();
        $application->add(new UpdateSlugsCommand($service));

        $command = $application->find('app:posts:update-slugs');
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('Successfully updated 2 post slug(s).', $tester->getDisplay());
    }

    public function testUpdateSlugsFailedHasCriticalErrors(): void
    {
        $service = $this->createMock(UpdateSlugsService::class);
        $service->expects($this->once())
            ->method('updateMissingSlugs')
            ->willReturn(new OperationResult(2, ['Critical error updating slug for post #1'], []));

        $application = new Application();
        $application->add(new UpdateSlugsCommand($service));

        $command = $application->find('app:posts:update-slugs');
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('Critical error updating slug', $tester->getDisplay());
    }
}
