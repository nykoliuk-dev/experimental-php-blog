<?php
declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

class Comment
{
    public function __construct(
        private ?int $id,
        private int $postId,
        private ?int $userId,
        private string $content,
        private string $createdAt,
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->content) === '') {
            throw new InvalidArgumentException('Comment cannot be empty');
        }

        if (strlen($this->content) < 3) {
            throw new InvalidArgumentException('Comment must be at least 3 characters');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $this->createdAt)) {
            throw new InvalidArgumentException('Invalid createdAt datetime format');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}