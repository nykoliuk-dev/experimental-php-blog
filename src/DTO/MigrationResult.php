<?php
declare(strict_types=1);

namespace App\DTO;
final class MigrationResult
{
    public function __construct(
        private readonly int $migratedCount,
        private readonly array $criticalErrors = [],
        private readonly array $validationErrors = []
    ) {}

    public function getMigratedCount(): int
    {
        return $this->migratedCount;
    }

    public function getCriticalErrors(): array
    {
        return $this->criticalErrors;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->criticalErrors) || !empty($this->validationErrors);
    }

    public function hasCriticalErrors(): bool
    {
        return !empty($this->criticalErrors);
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }
}