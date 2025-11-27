<?php
declare(strict_types=1);

namespace Model;

use App\Model\User;
use App\Model\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Model\User
 */
class UserTest extends TestCase
{
    /**
     * @dataProvider validIdProvider
     */
    public function testCreatesUserWithValidData(?UserId $id): void
    {
        $user = new User(
            id: $id,
            username: 'roman',
            email: 'test@example.com',
            passwordHash: str_repeat('a', 40),
            createdAt: '2025-11-07 10:00:00'
        );

        if ($id === null) {
            $this->assertNull($user->getId());
        } else {
            $this->assertSame(1, $user->getId()->value());
        }

        $this->assertSame('roman', $user->getUsername());
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame(str_repeat('a', 40), $user->getPasswordHash());
        $this->assertSame('2025-11-07 10:00:00', $user->getCreatedAt());
    }

    public function testThrowsExceptionWhenUsernameIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        new User(
            id: new UserId(1),
            username: '',
            email: 'test@example.com',
            passwordHash: str_repeat('x', 40),
            createdAt: '2025-11-07 10:00:00'
        );
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testThrowsExceptionWhenEmailIsInvalid(string $email): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new User(
            id: new UserId(1),
            username: 'user',
            email: $email,
            passwordHash: str_repeat('x', 40),
            createdAt: '2025-11-07 10:00:00'
        );
    }

    public function testThrowsExceptionWhenPasswordHashTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password hash is too short');

        new User(
            id: new UserId(1),
            username: 'user',
            email: 'test@example.com',
            passwordHash: 'short',
            createdAt: '2025-11-07 10:00:00'
        );
    }

    /**
     * @dataProvider invalidDatetimeProvider
     */
    public function testThrowsExceptionWhenCreatedAtIsInvalid(string $date): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid createdAt datetime format');

        new User(
            id: new UserId(1),
            username: 'user',
            email: 'test@example.com',
            passwordHash: str_repeat('x', 40),
            createdAt: $date
        );
    }

    public static function validIdProvider(): array
    {
        return [
            'new user (id=null)' => [null],
            'existing user' => [new UserId(1)],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['invalid'],
            ['missing@domain'],
            ['@missinglocal.com'],
            ['test@'],
        ];
    }

    public static function invalidDatetimeProvider(): array
    {
        return [
            ['2025-11-07'],
            ['07.11.2025 10:00:00'],
            ['bad'],
            ['2025/11/07 10:00:00'],
        ];
    }
}