<?php
declare(strict_types=1);

namespace Model\ValueObject;

use App\Model\ValueObject\CommentId;
use PHPUnit\Framework\TestCase;

class CommentIdTest extends TestCase
{
    public function testCreatesCommentIdWithValidDataAndReturnCorrectValue(): void
    {
        $id = 1;
        $commentId = new CommentId(1);
        $this->assertSame($id, $commentId->value());
    }

    /**
     * @dataProvider invalidIntIdProvider
     */
    public function testThrowsExceptionWhenInvalidIntId(int  $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('CommentId must be a positive integer.');
        new CommentId($id);
    }

    /**
     * @dataProvider wrongTypeProvider
     */
    public function testThrowsExceptionWhenIdHasWrongType(mixed $id): void
    {
        $this->expectException(\TypeError::class);
        new CommentId($id);
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
