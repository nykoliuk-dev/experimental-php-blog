<?php
declare(strict_types=1);

namespace App\Core;

final class Config
{
    public function __construct(
        private string $root,
        private string $cache,
        private string $configDir,
        private string $app,
        private string $view,
        private string $helpers,
        private string $gallery,
        private array $env
    ) {}

    public function getRoot(): string { return $this->root; }
    public function getCache(): string { return $this->cache; }
    public function getConfigDir(): string { return $this->configDir; }
    public function getApp(): string { return $this->app; }
    public function getView(): string { return $this->view; }
    public function getHelpers(): string { return $this->helpers; }
    public function getGallery(): string { return $this->gallery; }

    public function getEnv(string $key, mixed $default = null): mixed
    {
        return $this->env[$key] ?? $default;
    }
}