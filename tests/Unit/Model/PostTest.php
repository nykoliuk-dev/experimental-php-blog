<?php
declare(strict_types=1);

namespace Unit\Model;

use App\Model\Post;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use PHPUnit\Framework\TestCase;
use Tests\Factory\PostFactory;

/**
 * @covers \App\Model\Post
 */
class PostTest extends TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testCreatesPostWithValidData(?PostId $id): void
    {
        $post = PostFactory::create($id);

        if($id === null){
            $this->assertNull($post->getId());
        }else{
            $this->assertSame(1, $post->getId()->value());
        }

        $this->assertNull($post->getUserId());
        $this->assertSame('2025-11-07', $post->getDate());
        $this->assertSame('Title', $post->getTitle());
        $this->assertSame('title', $post->getSlug());
        $this->assertSame('Content', $post->getContent());
        $this->assertSame('img.jpg', $post->getImgName());
    }

    /**
     * @dataProvider invalidTitleProvider
     */
    public function testThrowsExceptionWhenInvalidTitle(string $title, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        new Post(
            id: new PostId(1),
            userId: new UserId(1),
            date: '2025-11-07',
            title: $title,
            slug: 'title',
            content: 'content',
            imageName: 'img.jpg'
        );

    }

    /**
     * @dataProvider invalidSlugProvider
     */
    public function testThrowsExceptionWhenSlugIsInvalid(string $slug): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid slug format');

        new Post(
            id: new PostId(1),
            userId: new UserId(1),
            date: '2025-11-07',
            title: 'Title',
            slug: $slug,
            content: 'content',
            imageName: 'img.jpg'
        );

    }

    public function testThrowsExceptionWhenContentIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content cannot be empty');

        new Post(
            id: new PostId(1),
            userId: new UserId(1),
            date: '2025-11-07',
            title: 'Title',
            slug: 'title',
            content: '',
            imageName: 'img.jpg'
        );
    }

    /**
     * @dataProvider invalidImageNameProvider
     */
    public function testThrowsExceptionWhenInvalidImageName(string $imgName): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid image format');

        new Post(
            id: new PostId(1),
            userId: new UserId(1),
            date: '2025-11-07',
            title: 'Title',
            slug: 'title',
            content: 'Content',
            imageName: $imgName
        );
    }

    public static function validIdProvider(): array
    {
        return [
            'new post (id = null)' => [null],
            'post exists (id = PostId object)' => [new PostId(1)],
        ];
    }

    public static function invalidTitleProvider(): array
    {
        return [
            'empty title' => ['', 'Title cannot be empty'],
            'too short title' => ['Hi', 'Title must be at least 3 characters'],
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

    public static function invalidImageNameProvider(): array
    {
        return [
            'empty name' => [''],
            'invalid file extension' => ['img.pdf'],
        ];
    }
}
