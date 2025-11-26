<?php
declare(strict_types=1);

namespace App\Service\Interfaces;

interface FileMoverInterface
{
    public function move(string $from, string $to): bool;
}