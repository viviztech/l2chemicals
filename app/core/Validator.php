<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function required(mixed $value): bool { return trim((string) $value) !== ''; }
    public static function email(mixed $value): bool { return filter_var($value, FILTER_VALIDATE_EMAIL) !== false; }
    public static function max(mixed $value, int $length): bool { return mb_strlen((string) $value) <= $length; }
    public static function phone(mixed $value): bool { return preg_match('/^[0-9+()\-\s]{7,20}$/', (string) $value) === 1; }
}

