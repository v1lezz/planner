<?php
namespace App\Models;
use App\Core\Database;

class User {
    public static function findById(int $id): ?array {
        $row = Database::query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
        return $row ?: null;
    }
    public static function findByEmail(string $email): ?array {
        $row = Database::query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
        return $row ?: null;
    }
    public static function create(string $name, string $email, string $hash, int $roleId): ?int {
        try {
            $stmt = Database::call('sp_register_user', [$name, $email, $hash, $roleId]);
            $row = $stmt->fetch();
            return $row['new_user_id'] ?? null;
        } catch (\PDOException $e) {
            return null;
        }
    }
    public static function all(): array {
        return Database::query("SELECT u.*, rg.name as role_name FROM users u JOIN roles_global rg ON rg.id = u.global_role_id ORDER BY u.id DESC")->fetchAll();
    }
    public static function delete(int $id): void {
        Database::query("DELETE FROM users WHERE id = ?", [$id]);
    }
    public static function updateRole(int $id, int $roleId): void {
        Database::query("UPDATE users SET global_role_id = ? WHERE id = ?", [$roleId, $id]);
    }
}
