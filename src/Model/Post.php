<?php
declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

class Post
{
    public function __construct(
        private int $id,
        private string $date,
        private string $title,
        private string $content,
        private string $imageName,
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->title) === '') {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        if (strlen($this->title) < 3) {
            throw new InvalidArgumentException('Title must be at least 3 characters');
        }

        if (trim($this->content) === '') {
            throw new InvalidArgumentException('Content cannot be empty');
        }

        if (!preg_match('/\.(jpg|jpeg|png|gif|webp|avif|heic)$/i', $this->imageName)) {
            throw new InvalidArgumentException('Invalid image format');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getContent(): string
    {
        return $this->content;
    }

    public function getImgName(): string
    {
        return $this->imageName;
    }
}