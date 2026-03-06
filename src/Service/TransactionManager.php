<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Interface\TransactionManagerInterface;

class TransactionManager implements TransactionManagerInterface
{
    public function __construct(private \PDO $pdo) {}

    public function wrap(callable $callback)
    {
        try {
            $this->pdo->beginTransaction();
            $result = $callback();
            $this->pdo->commit();
            return $result;

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}