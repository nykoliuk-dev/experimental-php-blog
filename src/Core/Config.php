<?php
declare(strict_types=1);

namespace App\Core;

final class Config
{
    public function __construct(
        public readonly string $root,
        public readonly string $cache,
        public readonly string $configDir,
        public readonly string $app,
        public readonly string $view,
        public readonly string $helpers,
        public readonly string $gallery,
        public readonly array $env
    ) {}

    public function dbPath(): string
    {
        return $this->env['db_path'];
    }
}