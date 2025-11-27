<?php
declare(strict_types=1);

namespace Unit\ValueObject;

use App\ValueObject\Pagination;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ValueObject\Pagination
 */
class PaginationTest extends TestCase
{
    public function testCreatesPaginationWithDefaultValues(): void
    {
        $pagination = new Pagination();

        $this->assertSame(20, $pagination->limit());
        $this->assertSame(0, $pagination->offset());
    }

    /**
     * @dataProvider validPaginationProvider
     */
    public function testCreatesPaginationWithValidData(int $limit, int $offset): void
    {
        $pagination = new Pagination($limit, $offset);

        $this->assertSame($limit, $pagination->limit());
        $this->assertSame($offset, $pagination->offset());
    }
    /**
     * @dataProvider invalidLimitProvider
     */
    public function testThrowsExceptionWhenInvalidLimit(int $limit): void
    {
        $offset = 0;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid limit: $limit");

        new Pagination($limit, $offset);

    }

    /**
     * @dataProvider invalidOffsetProvider
     */
    public function testThrowsExceptionWhenInvalidOffset(int $offset): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid offset: $offset");

        new Pagination(20, $offset);
    }

    public static function validPaginationProvider(): array
    {
        return [
            [20, 0],
            [100, 0],
            [50, 10],
            [25, 999],
        ];
    }

    public static function invalidLimitProvider(): array
    {
        return [
            'less than 20' => [10],
            'more than 20' => [110],
        ];
    }

    public static function invalidOffsetProvider(): array
    {
        return [
            'negative offset' => [-1],
            'large negative'  => [-999],
        ];
    }
}
