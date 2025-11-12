<?php
declare(strict_types=1);

namespace App\Core;

use App\Repository\PostRepositoryInterface;
use Twig\Environment;

abstract class Controller
{
    public function __construct(
        protected PostRepositoryInterface $repo,
        protected Environment             $twig
    ) {}

    protected function render(string $template, array $data): void
    {
        echo $this->twig->render($template . '.twig', $data);
    }
}