<?php
namespace App\Models;

use App\Core\Database;

class Dashboard {
    public static function tasksByStatus(): array {
        $sql = "SELECT s.name AS status_name, COUNT(t.id) AS task_count
                FROM statuses s
                LEFT JOIN tasks t ON t.status_id = s.id
                GROUP BY s.id
                ORDER BY s.id";
        return Database::query($sql)->fetchAll();
    }
}
