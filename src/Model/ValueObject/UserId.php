<?php
declare(strict_types=1);

namespace App\Model\ValueObject;

final class UserId
{
    public function __construct(
        private int $value
    ) {
        if ($value < 1) {
            throw new \InvalidArgumentException("UserId must be a positive integer.");
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}