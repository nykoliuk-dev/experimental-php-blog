<?php
declare(strict_types=1);

namespace App\Service;

use PDO;
use PDOStatement;

class DatabaseService
{
    public function __construct(private PDO $pdo) {}

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute($params)) {
            $this->checkError($stmt);
        }
        return $stmt;
    }

    private function checkError(\PDOStatement $stmt): void
    {
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] !== PDO::ERR_NONE) {
            throw new \RuntimeException("Database error: {$errorInfo[2]}");
        }
    }

    /**
     * @return Post[]
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * @return Post[]
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}