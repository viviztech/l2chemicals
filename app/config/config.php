<?php

declare(strict_types=1);

$envFile = dirname(__DIR__, 2) . '/.env';
$values = [];
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $values[$key] = trim($value, "\"'");
    }
}

$env = static fn (string $key, mixed $default = null): mixed => $values[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
$bool = static fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOL);

return [
    'app' => [
        'name' => 'L2 Chemicals',
        'env' => (string) $env('APP_ENV', 'production'),
        'debug' => $bool($env('APP_DEBUG', false)),
        'url' => rtrim((string) $env('APP_URL', ''), '/'),
        'key' => (string) $env('APP_KEY', ''),
        'timezone' => (string) $env('APP_TIMEZONE', 'Asia/Kolkata'),
    ],
    'database' => [
        'host' => (string) $env('DB_HOST', '127.0.0.1'),
        'port' => (int) $env('DB_PORT', 3306),
        'name' => (string) $env('DB_NAME', 'l2chemicals'),
        'user' => (string) $env('DB_USER', ''),
        'pass' => (string) $env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => 'l2_session',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'same_site' => 'Lax',
    ],
];
