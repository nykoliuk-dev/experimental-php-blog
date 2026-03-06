<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\ValueObject\CommentId;
use App\Model\ValueObject\PostId;
use App\Model\ValueObject\UserId;
use InvalidArgumentException;

class Comment
{
    public function __construct(
        private ?CommentId $id,
        private PostId $postId,
        private ?UserId $userId,
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

    public function getId(): ?CommentId
    {
        return $this->id;
    }

    public function getPostId(): PostId
    {
        return $this->postId;
    }

    public function getUserId(): ?UserId
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