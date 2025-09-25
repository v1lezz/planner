<?php
namespace App\Models;
use App\Core\Database;

class Board {
    public static function allForUser(?int $userId): array {
        if ($userId === null) {
            return Database::query("SELECT * FROM boards ORDER BY id DESC")->fetchAll();
        }
        $sql = "SELECT b.* FROM boards b
                LEFT JOIN user_board ub ON ub.board_id = b.id AND ub.user_id = ?
                WHERE ub.id IS NOT NULL OR EXISTS(SELECT 1 FROM users u WHERE u.id=? AND u.global_role_id=4)
                ORDER BY b.id DESC";
        return Database::query($sql, [$userId, $userId])->fetchAll();
    }

    public static function all(): array {
        return Database::query("SELECT * FROM boards ORDER BY id DESC")->fetchAll();
    }

    public static function find(int $id): ?array {
        $row = Database::query("SELECT * FROM boards WHERE id = ?", [$id])->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $key): ?int {
        $stmt = Database::call('sp_create_board', [$name, $key]);
        $row = $stmt->fetch();
        return $row['new_board_id'] ?? null;
    }

    public static function update(int $id, string $name, string $key): void {
        Database::call('sp_update_board', [$id, $name, $key]);
    }

    public static function deleteCascade(int $id): void {
        Database::call('sp_delete_board_cascade', [$id]);
    }

    public static function members(int $boardId): array {
        $sql = "SELECT ub.user_id, u.full_name, u.email, ub.role_id, br.name AS role_name
                FROM user_board ub
                JOIN users u ON u.id = ub.user_id
                JOIN board_roles br ON br.id = ub.role_id
                WHERE ub.board_id = ?
                ORDER BY u.full_name";
        return Database::query($sql, [$boardId])->fetchAll();
    }

    public static function setMember(int $userId, int $boardId, int $roleId): void {
        Database::call('sp_add_user_to_board', [$userId, $boardId, $roleId]);
    }

    public static function removeMember(int $userId, int $boardId): void {
        Database::call('sp_remove_user_from_board', [$userId, $boardId]);
    }
}
