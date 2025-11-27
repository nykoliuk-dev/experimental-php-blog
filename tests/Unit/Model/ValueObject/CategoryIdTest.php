<?php
declare(strict_types=1);

namespace Model\ValueObject;

use App\Model\ValueObject\CategoryId;
use PHPUnit\Framework\TestCase;

class CategoryIdTest extends TestCase
{
    public function testCreatesCategoryIdWithValidDataAndReturnCorrectValue(): void
    {
        $id = 1;
        $categoryId = new CategoryId(1);
        $this->assertSame($id, $categoryId->value());
    }

    /**
     * @dataProvider invalidIntIdProvider
     */
    public function testThrowsExceptionWhenInvalidIntId(int  $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('CategoryId must be a positive integer.');
        new CategoryId($id);
    }

    /**
     * @dataProvider wrongTypeProvider
     */
    public function testThrowsExceptionWhenIdHasWrongType(mixed $id): void
    {
        $this->expectException(\TypeError::class);
        new CategoryId($id);
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
