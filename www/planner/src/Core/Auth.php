<?php
namespace App\Core;
use App\Models\User;
use DateTimeImmutable;

class Auth {
    private static array $config;

    public static function init(array $appConfig): void {
        self::$config = $appConfig;
        if (!isset($_SESSION['user']) && isset($_COOKIE['remember'])) {
            [$selector, $validator] = explode(':', $_COOKIE['remember'], 2);
            $row = Database::query("SELECT * FROM auth_tokens WHERE selector = ? AND expires_at > NOW()", [$selector])->fetch();
            if ($row && hash_equals($row['validator_hash'], hash('sha256', $validator))) {
                $user = User::findById((int)$row['user_id']);
                if ($user) {
                    $_SESSION['user'] = $user;
                    self::refreshRememberToken((int)$row['user_id'], $selector);
                }
            }
        }
    }

    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function checkRole(string $role): bool {
        $user = self::user();
        if (!$user) return false;
        $roles = ['guest'=>1, 'client'=>2, 'staff'=>3, 'admin'=>4];
        return ($user['global_role_id'] ?? 1) >= ($roles[$role] ?? 1);
    }

    public static function requireRole(string $role): void {
        if (!self::checkRole($role)) {
            header('Location: /login');
            exit;
        }
    }

    public static function login(string $email, string $password, bool $remember): bool {
        $user = User::findByEmail($email);
        if (!$user) return false;
        if (!password_verify($password, $user['password_hash'])) return false;
        $_SESSION['user'] = $user;
        if ($remember) {
            $selector = bin2hex(random_bytes(6));
            self::createRememberToken($user['id'], $selector);
        }
        return true;
    }

    public static function logout(): void {
        if (isset($_COOKIE['remember'])) {
            [$selector, $validator] = explode(':', $_COOKIE['remember'], 2);
            Database::query("DELETE FROM auth_tokens WHERE selector = ?", [$selector]);
            setcookie('remember', '', time() - 3600, '/', self::$config['cookie_domain'], self::$config['cookie_secure'], true);
        }
        unset($_SESSION['user']);
    }

    private static function createRememberToken(int $userId, string $selector): void {
        $validator = bin2hex(random_bytes(16));
        $hash = hash('sha256', $validator);
        $days = self::$config['remember_days'] ?? 30;
        $expires = (new DateTimeImmutable("+{$days} days"))->format('Y-m-d H:i:s');
        Database::query("INSERT INTO auth_tokens (user_id, selector, validator_hash, expires_at) VALUES (?,?,?,?)",
            [$userId, $selector, $hash, $expires]);
        $cookie = $selector . ':' . $validator;
        setcookie('remember', $cookie, [
            'expires' => time() + 86400*$days,
            'path' => '/',
            'domain' => self::$config['cookie_domain'],
            'secure' => self::$config['cookie_secure'],
            'httponly' => true,
            'samesite' => self::$config['cookie_samesite'] ?? 'Lax',
        ]);
    }

    private static function refreshRememberToken(int $userId, string $selector): void {
        $validator = bin2hex(random_bytes(16));
        $hash = hash('sha256', $validator);
        $days = self::$config['remember_days'] ?? 30;
        $expires = (new DateTimeImmutable("+{$days} days"))->format('Y-m-d H:i:s');
        Database::query("UPDATE auth_tokens SET validator_hash=?, expires_at=? WHERE selector=?",
            [$hash, $expires, $selector]);
        $cookie = $selector . ':' . $validator;
        setcookie('remember', $cookie, [
            'expires' => time() + 86400*$days,
            'path' => '/',
            'domain' => self::$config['cookie_domain'],
            'secure' => self::$config['cookie_secure'],
            'httponly' => true,
            'samesite' => self::$config['cookie_samesite'] ?? 'Lax',
        ]);
    }
}
