<?php

namespace Unit\Service;

use App\Service\DatabaseService;
use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseServiceTest extends TestCase
{
    public function testQueryThrowsExceptionOnSqlError(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $service = new DatabaseService($pdo);

        $this->expectException(\RuntimeException::class);

        $service->query("INVALID SQL");
    }

    public function testFetchOneReturnsNullWhenNoRows(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE test (id INTEGER)");

        $service = new DatabaseService($pdo);

        $result = $service->fetchOne("SELECT * FROM test WHERE id = 1");

        $this->assertNull($result);
    }

    public function testFetchAllReturnsRows(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE test (id INTEGER, name TEXT)");
        $pdo->exec("INSERT INTO test (id, name) VALUES (1, 'A'), (2, 'B')");

        $service = new DatabaseService($pdo);

        $rows = $service->fetchAll("SELECT * FROM test ORDER BY id");

        $this->assertCount(2, $rows);
        $this->assertSame(1 , $rows[0]['id']);
        $this->assertSame('A', $rows[0]['name']);
    }
}
