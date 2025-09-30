<?php
declare(strict_types=1);

return [
    'debug' => (bool)($_ENV['DEBUG'] ?? false),
    'db_path' => $_ENV['DB_PATH'] ?? '',
];