<?php
declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;

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
    ) {
        $this->validateEnv();
    }

    private function validateEnv(): void
    {
        $required = [
            'DB_PATH',
            'DB_DRIVER',
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_CHARSET',
        ];

        foreach ($required as $key) {
            if (empty($this->env[$key])) {
                throw new \InvalidArgumentException("Missing required env variable: {$key}");
            }
        }

        if (!empty($this->env['DB_PORT']) && !is_numeric($this->env['DB_PORT'])) {
            throw new \InvalidArgumentException("DB_PORT must be numeric");
        }

        // Validate path to JSON file
        if (!str_starts_with($this->env['DB_PATH'], '/')) {
            throw new \InvalidArgumentException("DB_PATH must be an absolute path starting with '/'");
        }

        $allowedDrivers = ['mysql', 'pgsql', 'sqlite', 'mariadb'];
        if (!in_array(strtolower($this->env['DB_DRIVER']), $allowedDrivers, true)) {
            throw new \InvalidArgumentException("Unsupported DB_DRIVER: {$this->env['DB_DRIVER']}");
        }
    }
    public function dbPath(): string
    {
        return $this->root . $this->env['DB_PATH'];
    }

    public function getDsn(): string
    {
        $driver = $this->env['DB_DRIVER'];
        $host = $this->env['DB_HOST'];
        $dbname = $this->env['DB_NAME'];
        $charset = $this->env['DB_CHARSET'];

        return "$driver:host=$host;dbname=$dbname;charset=$charset";
    }
}