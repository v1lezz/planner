<?php
namespace App\Models;

use App\Core\Database;

class Epic {
    public static function listByBoard(int $boardId): array {
        return Database::query(
            "SELECT id, title FROM epics WHERE board_id = ? ORDER BY id DESC",
            [$boardId]
        )->fetchAll();
    }

    public static function listByBoardDetailed(int $boardId): array {
        $sql = "SELECT e.id, e.title, e.description, e.status_id, s.name AS status_name,
                       e.owner_user_id, u.full_name AS owner_name
                FROM epics e
                JOIN statuses s ON s.id = e.status_id
                LEFT JOIN users u ON u.id = e.owner_user_id
                WHERE e.board_id = ?
                ORDER BY e.id DESC";
        return Database::query($sql, [$boardId])->fetchAll();
    }

    public static function create(int $boardId, string $title, ?string $desc, ?int $ownerUserId, int $statusId): ?int {
        $stmt = Database::call('sp_create_epic', [$boardId, $title, $desc, $ownerUserId, $statusId]);
        $row  = $stmt->fetch();
        return $row['new_epic_id'] ?? null;
    }

    public static function update(int $id, string $title, ?string $desc, ?int $ownerUserId, int $statusId): void {
        Database::call('sp_update_epic', [$id, $title, $desc, $ownerUserId, $statusId]);
    }

    public static function deleteCascade(int $epicId): void {
        Database::call('sp_delete_epic_cascade', [$epicId]); // уже есть в миграции 002
    }
}
