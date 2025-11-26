<?php
declare(strict_types=1);

namespace App\Repository\Interfaces;

use App\Model\User;
use App\Model\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function getUserById(UserId $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function getUserByUsername(string $username): ?User;
    public function addUser(User $user): ?User;
}