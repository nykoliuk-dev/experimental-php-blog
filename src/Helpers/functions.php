<?php
declare(strict_types=1);

function render(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require APP . '/View/' . $template . '.php';
    $content = ob_get_clean();

    require APP . '/View/layout.php';
}