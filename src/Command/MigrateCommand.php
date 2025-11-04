<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\Post;
use App\Service\PostMigrationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $output->writeln('<info>Starting migration...</info>');

        $count = $this->service->migrate();

        $output->writeln("$count posts successfully added");

        return Command::SUCCESS;
    }

    private function postReadyToMigrate(Post $post): bool
    {
        if ($post->getTitle() === '' || $post->getContent() === '') {
            return false;
        }
        return true;
    }
}