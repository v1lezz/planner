<?php
namespace App\Models;
use App\Core\Database;

class RefData {
    public static function getStatuses(): array {
        return Database::query("SELECT * FROM statuses ORDER BY id")->fetchAll();
    }
    public static function getPriorities(): array {
        return Database::query("SELECT * FROM priorities ORDER BY sort_order")->fetchAll();
    }
    public static function getTypes(): array {
        return Database::query("SELECT * FROM task_types ORDER BY id")->fetchAll();
    }
    public static function getBoardRoles(): array {
        return Database::query("SELECT * FROM board_roles ORDER BY id")->fetchAll();
    }
    public static function addToRef(string $table, string $name): void {
        $allowed = ['statuses','priorities','task_types','board_roles'];
        if (!in_array($table, $allowed, true)) return;
        if ($table === 'priorities') {
            Database::query("INSERT INTO priorities (name, sort_order) VALUES (?, 0)", [$name]);
        } else {
            Database::query("INSERT INTO {$table} (name) VALUES (?)", [$name]);
        }
    }
    public static function deleteFromRef(string $table, int $id): void {
        $allowed = ['statuses','priorities','task_types','board_roles'];
        if (!in_array($table, $allowed, true)) return;
        Database::query("DELETE FROM {$table} WHERE id = ?", [$id]);
    }
}
