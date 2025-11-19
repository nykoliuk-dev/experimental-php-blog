<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\UpdateSlugsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * CLI command to generate and update slugs for existing posts without one.
 */
class UpdateSlugsCommand extends Command
{
    /**
     * Command name in console
     */
    protected static $defaultName = 'app:posts:update-slugs';

    public function __construct(
        private UpdateSlugsService $service,
    )
    {
        parent::__construct(self::$defaultName);
    }


    /**
     * Command description
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generates and updates missing slugs for existing posts.');
    }

    /**
     * Executing a command
     *
     * @param InputInterface $input Arguments and options
     * @param OutputInterface $output Console output
     * @return int Command exit code (0 = success)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Post Slugs Update Command');
        $io->info('Scanning posts without slugs and starting update...');

        $result = $this->service->updateMissingSlugs();

        if ($result->hasValidationErrors()){
            $io->section('Skipped Posts (Validation Issues)');
            $io->warning(implode("\n", $result->getValidationErrors()));
        }

        if ($result->hasCriticalErrors()){
            $io->section('Critical Errors');
            $io->error(implode("\n", $result->getCriticalErrors()));
            return Command::FAILURE;
        }

        if ($result->getMigratedCount() > 0) {
            $io->success("Successfully updated {$result->getMigratedCount()} post slug(s).");
        } else {
            $io->success("No posts found requiring slug updates. All done!");
        }

        return Command::SUCCESS;
    }
}