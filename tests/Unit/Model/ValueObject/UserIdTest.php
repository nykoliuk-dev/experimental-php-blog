<?php
declare(strict_types=1);

namespace Model\ValueObject;

use App\Model\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\ValueObject\UserId
 */
class UserIdTest extends TestCase
{
    public function testCreatesUserIdWithValidDataAndReturnCorrectValue(): void
    {
        $id = 1;
        $userId = new UserId($id);
        $this->assertSame($id, $userId->value());
    }

    /**
     * @dataProvider invalidIntIdProvider
     */
    public function testThrowsExceptionWhenInvalidIntId(int  $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('UserId must be a positive integer.');
        new UserId($id);
    }

    /**
     * @dataProvider wrongTypeProvider
     */
    public function testThrowsExceptionWhenIdHasWrongType(mixed $id): void
    {
        $this->expectException(\TypeError::class);
        new UserId($id);
    }

    public static function invalidIntIdProvider(): array
    {
        return [
            'negative id' => [-5],
            'zero id' => [0],
        ];
    }

    public static function wrongTypeProvider(): array
    {
        return [
            'float' => [1.5],
            'string' => ['10'],
            'string with letters' => ['4cats'],
            'empty string' => [''],
            'null' => [null],
            'bool true' => [true],
            'bool false' => [false],
            'array' => [[]],
            'object' => [(object)[]],
        ];
    }
}
