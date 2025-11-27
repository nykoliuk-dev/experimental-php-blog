<?php
declare(strict_types=1);

namespace Model\ValueObject;

use App\Model\ValueObject\PostId;
use PHPUnit\Framework\TestCase;

class PostIdTest extends TestCase
{
    public function testCreatesPostIdWithValidDataAndReturnCorrectValue(): void
    {
        $id = 1;
        $postId = new PostId(1);
        $this->assertSame($id, $postId->value());
    }

    /**
     * @dataProvider invalidIntIdProvider
     */
    public function testThrowsExceptionWhenInvalidIntId(int  $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('PostId must be a positive integer.');
        new PostId($id);
    }

    /**
     * @dataProvider wrongTypeProvider
     */
    public function testThrowsExceptionWhenIdHasWrongType(mixed $id): void
    {
        $this->expectException(\TypeError::class);
        new PostId($id);
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
