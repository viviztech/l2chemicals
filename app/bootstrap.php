<?php

declare(strict_types=1);

use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $adminPrefix = 'App\\Admin\\';
    if (str_starts_with($class, $adminPrefix)) {
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($adminPrefix)));
        $file = BASE_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $relative . '.php';
        if (is_file($file)) require $file;
        return;
    }
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $relative . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/core/Helpers.php';

$config = require BASE_PATH . '/app/config/config.php';
date_default_timezone_set((string) $config['app']['timezone']);
ini_set('display_errors', $config['app']['debug'] ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/app.log');
error_reporting(E_ALL);

set_exception_handler(static function (Throwable $exception) use ($config): void {
    error_log($exception->__toString());
    http_response_code(500);
    $message = $config['app']['debug'] ? $exception->getMessage() : null;
    require BASE_PATH . '/app/views/errors/500.php';
});

Session::start($config['session']);
