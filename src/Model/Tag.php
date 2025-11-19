<?php
declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

class Tag
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $slug,
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Tag name cannot be empty');
        }

        if (strlen($this->name) < 2) {
            throw new InvalidArgumentException('Tag name must be at least 2 characters');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $this->slug)) {
            throw new InvalidArgumentException('Invalid slug format');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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