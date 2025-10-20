<?php
declare(strict_types=1);

namespace App\Model;

class Post
{
    public function __construct(
        private int $id,
        private string $date,
        private string $title,
        private string $content,
        private string $imageName,
    ) {}

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