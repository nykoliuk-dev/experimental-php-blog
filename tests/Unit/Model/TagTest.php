<?php
declare(strict_types=1);

namespace Model;

use App\Model\Tag;
use App\Model\ValueObject\TagId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Tag
 */
class TagTest extends TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testCreatesTagWithValidData(?TagId $id): void
    {
        $tag = new Tag(
            id: $id,
            name: 'Tech',
            slug: 'tech'
        );

        if ($id === null) {
            $this->assertNull($tag->getId());
        } else {
            $this->assertSame(1, $tag->getId()->value());
        }

        $this->assertSame('Tech', $tag->getName());
        $this->assertSame('tech', $tag->getSlug());
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testThrowsExceptionWhenInvalidName(string $name, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new Tag(
            id: new TagId(1),
            name: $name,
            slug: 'valid-slug'
        );
    }

    /**
     * @dataProvider invalidSlugProvider
     */
    public function testThrowsExceptionWhenSlugIsInvalid(string $slug): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid slug format');

        new Tag(
            id: new TagId(1),
            name: 'Tech',
            slug: $slug
        );
    }

    public static function validIdProvider(): array
    {
        return [
            'new tag (id=null)' => [null],
            'existing tag' => [new TagId(1)],
        ];
    }

    public static function invalidNameProvider(): array
    {
        return [
            'empty name' => [''   , 'Tag name cannot be empty'],
            'too short name' => ['T'  , 'Tag name must be at least 2 characters'],
        ];
    }

    public static function invalidSlugProvider(): array
    {
        return [
            'empty' => [''],
            'invalid char 1' => ['*bad'],
            'invalid char 2' => ['slug#1'],
            'contains space' => ['bad slug'],
        ];
    }
}