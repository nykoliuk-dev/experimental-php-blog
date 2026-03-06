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
    public function register(string $username, string $email, string $password): User
    {
        if ($this->repo->getUserByEmail($email)) {
            throw new \RuntimeException("Email already taken");
        }

        if ($this->repo->getUserByUsername($username)) {
            throw new \RuntimeException("Username already taken");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(
            id: null,
            username: $username,
            email: $email,
            passwordHash: $passwordHash,
            createdAt: date('Y-m-d H:i:s'),
        );

        return $this->repo->addUser($user);
    }

    public function login(string $email, string $password): User
    {
        $user = $this->repo->getUserByEmail($email);

        if (!$user) {
            throw new \RuntimeException("User not found");
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            throw new \RuntimeException("Invalid password");
        }

        return $user;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}