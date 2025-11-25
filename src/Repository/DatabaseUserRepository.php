<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use App\Model\ValueObject\UserId;
use App\Service\DatabaseService;

class DatabaseUserRepository implements UserRepositoryInterface
{
    public function __construct(private DatabaseService $db) {}

    public function getUserById(UserId $id): ?User
    {
        $sql = "SELECT * FROM `users` WHERE id = :id";
        $data = $this->db->fetchOne($sql, ['id' => $id->value()]);

        return $data ? $this->mapRowToUser($data) : null;
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM `users` WHERE email = :email";
        $data = $this->db->fetchOne($sql, ['email' => $email]);

        return $data ? $this->mapRowToUser($data) : null;
    }

    public function getUserByUsername(string $username): ?User
    {
        $sql = "SELECT * FROM `users` WHERE username = :username";
        $data = $this->db->fetchOne($sql, ['username' => $username]);

        return $data ? $this->mapRowToUser($data) : null;
    }

    public function addUser(User $user): ?User
    {
        $sql = "INSERT INTO `users` (username, email, password_hash, created_at)
                VALUES (:username, :email, :password_hash, :created_at)";

        $this->db->query($sql, [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'created_at' => $user->getCreatedAt(),
        ]);

        $id = new UserId($this->db->lastInsertId());
        return $this->getUserById($id);
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            new UserId((int)$row['id']),
            $row['username'],
            $row['email'],
            $row['password_hash'],
            $row['created_at']
        );
    }
}