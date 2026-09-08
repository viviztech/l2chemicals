<?php

declare(strict_types=1);

use App\Core\CSRF;
use App\Core\Session;

function config(?string $key = null, mixed $default = null): mixed
{
    static $config;
    $config ??= require BASE_PATH . '/app/config/config.php';
    if ($key === null) return $config;
    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) return $default;
        $value = $value[$segment];
    }
    return $value;
}

function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/'); }
function asset(string $path): string { return url('public/assets/' . ltrim($path, '/')); }
function upload_url(?string $path, string $fallback = 'images/placeholder.svg'): string { return $path ? url($path) : asset($fallback); }
function csrf_field(): string { return '<input type="hidden" name="_token" value="' . e(CSRF::token()) . '">'; }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function flash(string $key, mixed $value): void { Session::flash($key, $value); }
function flash_pull(string $key, mixed $default = null): mixed { return Session::pull($key, $default); }
function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    return trim($value, '-') ?: 'item-' . bin2hex(random_bytes(3));
}
function client_ip_hash(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return hash('sha256', $ip . '|' . (string) config('app.key'));
}

function view(string $name, array $data = [], string $layout = 'layouts/public'): void
{
    $viewFile = BASE_PATH . '/app/views/' . $name . '.php';
    if (!is_file($viewFile)) throw new RuntimeException('View not found.');
    extract($data, EXTR_SKIP);
    ob_start();
    require $viewFile;
    $content = (string) ob_get_clean();
    if ($layout === '') { echo $content; return; }
    require BASE_PATH . '/app/views/' . $layout . '.php';
}
