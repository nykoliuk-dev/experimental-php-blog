<?php
declare(strict_types=1);

namespace App\Core;

use App\Repository\PostRepository;

class Controller
{
    public function __construct(
        protected array $config,
        protected PostRepository $repo
    ) {}

    protected function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $this->config['paths']['view'] . '/' . $template . '.php';
        $content = ob_get_clean();

        require $this->config['paths']['view'] . '/layout.php';
    }
}