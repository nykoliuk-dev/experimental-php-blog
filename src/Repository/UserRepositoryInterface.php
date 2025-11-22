<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

interface UserRepositoryInterface
{
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function getUserByUsername(string $username): ?User;
    public function addUser(User $user): ?User;
}