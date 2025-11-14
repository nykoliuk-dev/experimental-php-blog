<?php
declare(strict_types=1);

namespace App\Service;

final class FileMover implements FileMoverInterface
{
    public function move(string $from, string $to): bool
    {
        return move_uploaded_file($from, $to);
    }
}