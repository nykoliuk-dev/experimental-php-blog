<?php
declare(strict_types=1);

namespace App\Service\Interface;

interface TransactionManagerInterface
{
    public function wrap(callable $callback);
}