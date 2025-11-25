<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\ValueObject\UserId;
use InvalidArgumentException;

class User
{
    public function __construct(
        private ?UserId $id,
        private string $username,
        private string $email,
        private string $passwordHash,
        private string $createdAt,
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->username) === '') {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (strlen($this->passwordHash) < 20) {
            throw new InvalidArgumentException('Password hash is too short');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $this->createdAt)) {
            throw new InvalidArgumentException('Invalid createdAt datetime format');
        }
    }

    public function getId(): ?UserId
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}