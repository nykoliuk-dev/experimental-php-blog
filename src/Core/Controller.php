<?php
declare(strict_types=1);

namespace App\Core;

use App\Service\Interface\CurrentUserProviderInterface;
use Twig\Environment;

abstract class Controller
{
    public function __construct(
        protected Environment $twig,
        protected CurrentUserProviderInterface $currentUserProvider
    ) {}

    protected function render(string $template, array $data): void
    {
        $currentUserId = $this->currentUserProvider->getCurrentUserId()?->value();

        $globalData = [
            'current_user_id' => $currentUserId,
        ];
        echo $this->twig->render($template . '.twig', array_merge($globalData, $data));
    }
}