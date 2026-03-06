<?php
declare(strict_types=1);

namespace Model;

use App\Model\Comment;
use App\Model\ValueObject\CommentId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\Comment
 */
class CommentTest extends TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testCreatesCommentWithValidData(?CommentId $id): void
    {
        $comment = new Comment(
            id: $id,
            postId: new PostId(5),
            userId: new UserId(2),
            content: 'Valid comment',
            createdAt: '2025-11-07 12:00:00',
        );

        if ($id === null) {
            $this->assertNull($comment->getId());
        } else {
            $this->assertSame(1, $comment->getId()->value());
        }

        $this->assertSame(5, $comment->getPostId()->value());
        $this->assertSame(2, $comment->getUserId()->value());
        $this->assertSame('Valid comment', $comment->getContent());
        $this->assertSame('2025-11-07 12:00:00', $comment->getCreatedAt());
    }

    public function testThrowsExceptionWhenContentIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment cannot be empty');

        new Comment(
            id: new CommentId(1),
            postId: new PostId(5),
            userId: new UserId(2),
            content: '',
            createdAt: '2025-11-07 12:00:00'
        );
    }

    public function testThrowsExceptionWhenContentIsTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment must be at least 3 characters');

        new Comment(
            id: new CommentId(1),
            postId: new PostId(5),
            userId: new UserId(2),
            content: 'Hi',
            createdAt: '2025-11-07 12:00:00'
        );
    }

    /**
     * @dataProvider invalidDatetimeProvider
     */
    public function testThrowsExceptionWhenCreatedAtHasInvalidFormat(string $date): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid createdAt datetime format');

        new Comment(
            id: new CommentId(1),
            postId: new PostId(5),
            userId: new UserId(2),
            content: 'Valid content',
            createdAt: $date
        );
    }

    public static function validIdProvider(): array
    {
        return [
            'new comment (id=null)' => [null],
            'existing comment' => [new CommentId(1)],
        ];
    }

    public static function invalidDatetimeProvider(): array
    {
        return [
            ['2025-11-07'], // no time
            ['07.11.2025 12:00:00'],
            ['2025/11/07 12:00:00'],
            ['invalid'],
        ];
    }
}
