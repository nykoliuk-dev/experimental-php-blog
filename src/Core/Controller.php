<?php
declare(strict_types=1);

namespace App\Core;

use App\Repository\PostRepository;
use Twig\Environment;

abstract class Controller
{
    public function __construct(
        protected array $config,
        protected PostRepository $repo,
        protected Environment $twig
    ) {}

    protected function render(string $template, array $data): void
    {
        echo $this->twig->render($template . '.twig', $data);
    }
}