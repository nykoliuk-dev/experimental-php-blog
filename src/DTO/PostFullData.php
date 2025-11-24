<?php
declare(strict_types=1);

namespace App\DTO;

use App\Model\Post;

final class PostFullData
{
    public function __construct(
        private readonly Post $post,
        private readonly array $tags,
        private readonly array $categories
    ) {}

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}