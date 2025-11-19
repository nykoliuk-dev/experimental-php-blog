<?php
declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

class Category
{
    public function __construct(
        private ?int $id,
        private ?int $parentId,
        private string $name,
        private string $slug,
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Category name cannot be empty');
        }

        if (strlen($this->name) < 2) {
            throw new InvalidArgumentException('Category name must be at least 2 characters');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $this->slug)) {
            throw new InvalidArgumentException('Invalid slug format');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}