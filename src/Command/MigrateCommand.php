<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\PostMigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * CLI command to migrate posts from JSON storage to the SQL database.
 */
class MigrateCommand extends Command
{
    /**
     * Command name in console
     */
    protected static $defaultName = 'app:migrate:posts';

    public function __construct(
        private PostMigrationService $service,
    )
    {
        parent::__construct('app:migrate:posts');
    }


    /**
     * Command description
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Migrate posts from JSON storage to the SQL database.');
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

        $io->title('Post Migration Command');
        $io->info('Starting migration...');

        $migrationResult = $this->service->migrate();

        if($migrationResult->hasValidationErrors()){
            $io->section('Validation Errors');
            $io->warning(implode("\n", $migrationResult->getValidationErrors()));
        }

        if($migrationResult->hasCriticalErrors()){
            $io->section('Critical Errors');
            $io->error(implode("\n", $migrationResult->getCriticalErrors()));
            return Command::FAILURE;
        }

        $io->success("{$migrationResult->getMigratedCount()} post(s) successfully added");

        return Command::SUCCESS;
    }
}