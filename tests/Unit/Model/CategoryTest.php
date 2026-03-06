<?php
declare(strict_types=1);

namespace Model;

use App\Model\Category;
use App\Model\ValueObject\CategoryId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Category
 */
class CategoryTest extends TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testCreatesPostWithValidData(?CategoryId $id): void
    {
        $category = new Category(
            id: $id,
            parentId: new CategoryId(1),
            name: 'Name',
            slug: 'valid-slug',
        );

        if($id === null){
            $this->assertNull($category->getId());
        }else{
            $this->assertSame(1, $category->getId()->value());
        }

        $this->assertSame(1, $category->getParentId()->value());
        $this->assertSame('Name', $category->getName());
        $this->assertSame('valid-slug', $category->getSlug());
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testThrowsExceptionWhenInvalidName(string $name, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new Category(
            id: new CategoryId(10),
            parentId: new CategoryId(1),
            name: $name,
            slug: 'valid-slug',
        );

    }

    /**
     * @dataProvider invalidSlugProvider
     */
    public function testThrowsExceptionWhenSlugIsInvalid(string $slug): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid slug format');

        new Category(
            id: new CategoryId(10),
            parentId: new CategoryId(1),
            name: 'Name',
            slug: $slug,
        );

    }

    public static function validIdProvider(): array
    {
        return [
            'new post (id = null)' => [null],
            'post exists (id = PostId object)' => [new CategoryId(1)],
        ];
    }

    public static function invalidNameProvider(): array
    {
        return [
            'empty name' => ['', 'Category name cannot be empty'],
            'too short name' => ['C', 'Category name must be at least 2 characters'],
        ];
    }

    public static function invalidSlugProvider(): array
    {
        return [
            'contains space' => ['*tit le'],
            'contains special char' => ['slug@name'],
            'empty slug' => [''],
            'invalid symbols' => ['slug#1'],
        ];
    }
}
