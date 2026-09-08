<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = (require BASE_PATH . '/app/config/config.php')['database'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']);

        try {
            self::$connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            throw new RuntimeException('The website is temporarily unavailable. Please try again shortly.');
        }

        return self::$connection;
    }

    private function __construct() {}
}

