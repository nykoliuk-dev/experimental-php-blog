<?php
declare(strict_types=1);

namespace App\Command;

use App\Model\Post;
use App\Repository\DatabasePostRepository;
use App\Repository\JsonPostRepository;
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
        private JsonPostRepository $jsonRepo,
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
        $posts = $this->jsonRepo->getPosts();

        $count = 0;
        foreach ($posts as $post){
            if($this->postReadyToMigrate($post)){
                $this->dbRepo->addPost($post);
                $count++;
            }else{
                $output->writeln("<error>Post №{$post->getId()} is not valid</error>");
            }
        }

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