<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepositoryInterface;

class AuthService
{
    public function __construct(private UserRepositoryInterface $repo)
    {
    }
    public function register(array $data): User
    {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = new User(
            id: null,
            username: $data['username'],
            email: $data['email'],
            passwordHash: $passwordHash,
            createdAt: date('Y-m-d H:i:s'),
        );

        return $this->repo->addUser($user);
    }

    public function login(string $email, string $password): User
    {
        // ...
    }

    public function logout(): void
    {
        // ...
    }

    public function verifyEmail(string $token): void
    {
        // ...
    }

    public function restorePassword(string $email): void
    {
        // ...
    }
}