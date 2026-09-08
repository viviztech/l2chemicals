<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Auth
{
    private const SESSION_KEY = 'admin_user_id';

    public static function attempt(string $identifier, string $password): bool
    {
        $db = Database::connection();
        $emailHash = hash('sha256', strtolower(trim($identifier)) . '|' . (string) config('app.key'));
        $ipHash = client_ip_hash();
        $rate = $db->prepare('SELECT COUNT(*) FROM login_attempts WHERE email_hash=:email AND ip_hash=:ip AND successful=0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)');
        $rate->execute(['email' => $emailHash, 'ip' => $ipHash]);
        if ((int) $rate->fetchColumn() >= 5) {
            return false;
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE (email=:email OR username=:username) AND status='ACTIVE' LIMIT 1");
        $stmt->execute(['email' => trim($identifier), 'username' => trim($identifier)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $valid = is_array($user) && password_verify($password, $user['password_hash']);

        $attempt = $db->prepare('INSERT INTO login_attempts (email_hash, ip_hash, successful) VALUES (:email,:ip,:successful)');
        $attempt->execute(['email' => $emailHash, 'ip' => $ipHash, 'successful' => $valid ? 1 : 0]);
        if (!$valid) return false;

        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = (int) $user['id'];
        $_SESSION['admin_user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
        $db->prepare('UPDATE users SET last_login_at=NOW() WHERE id=:id')->execute(['id' => $user['id']]);
        $db->prepare('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 2 DAY)')->execute();
        return true;
    }

    public static function check(): bool { return isset($_SESSION[self::SESSION_KEY], $_SESSION['admin_user']); }
    public static function user(): ?array { return self::check() ? $_SESSION['admin_user'] : null; }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Please sign in to continue.');
            redirect('admin/login');
        }
    }

    public static function requireRoles(array $roles): void
    {
        self::requireLogin();
        if (!in_array((string) (self::user()['role'] ?? ''), $roles, true)) {
            http_response_code(403);
            view('errors/403', ['title' => 'Access denied'], 'layouts/admin');
            exit;
        }
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION['admin_user']);
        session_regenerate_id(true);
    }
}
