<?php
declare(strict_types=1);

namespace Model\ValueObject;

use App\Model\ValueObject\TagId;
use PHPUnit\Framework\TestCase;

class TagIdTest extends TestCase
{
    public function testCreatesTagIdWithValidDataAndReturnCorrectValue(): void
    {
        $id = 1;
        $tagId = new TagId(1);
        $this->assertSame($id, $tagId->value());
    }

    /**
     * @dataProvider invalidIntIdProvider
     */
    public function testThrowsExceptionWhenInvalidIntId(int  $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('TagId must be a positive integer.');
        new TagId($id);
    }

    /**
     * @dataProvider wrongTypeProvider
     */
    public function testThrowsExceptionWhenIdHasWrongType(mixed $id): void
    {
        $this->expectException(\TypeError::class);
        new TagId($id);
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
