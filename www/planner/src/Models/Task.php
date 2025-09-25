<?php
namespace App\Models;
use App\Core\Database;
use App\Core\Mailer;

class Task {
    public static function byBoardGroupedByStatus(int $boardId): array {
        $rows = Database::query("SELECT * FROM v_task_full WHERE board_id = ? ORDER BY priority_sort DESC, id DESC", [$boardId])->fetchAll();
        $grouped = [];
        foreach ($rows as $r) { $grouped[$r['status_id']][] = $r; }
        return $grouped;
    }

    public static function create(int $boardId, ?int $epicId, string $title, string $desc, int $typeId, int $priorityId, int $statusId, int $authorId, ?int $assigneeId, ?string $due, ?int $sp, bool $notify): ?int {
        $stmt = Database::call('sp_create_task', [$boardId, $epicId, $title, $desc, $typeId, $priorityId, $statusId, $authorId, $assigneeId, $due, $sp]);
        $row = $stmt->fetch();
        $id = $row['new_task_id'] ?? null;
        if ($id && $notify && $assigneeId) {
            $assignee = Database::query("SELECT email, full_name FROM users WHERE id = ?", [$assigneeId])->fetch();
            if ($assignee) {
                $html = "<h2>New task assigned</h2><p><strong>{$title}</strong></p><p>{$desc}</p>";
                Mailer::sendHtml($assignee['email'], "You have a new task", $html);
            }
        }
        return $id;
    }

    public static function changeStatus(int $taskId, int $statusId): void {
        Database::call('sp_update_task_status', [$taskId, $statusId]);
    }

    public static function bulkDelete(int $boardId, array $ids): void {
        $csv = implode(',', array_map('intval', $ids));
        Database::call('sp_bulk_delete_tasks', [$boardId, $csv]);
    }

    public static function bulkChangeStatus(int $boardId, array $ids, int $statusId): void {
        $csv = implode(',', array_map('intval', $ids));
        Database::call('sp_bulk_update_task_status', [$boardId, $csv, $statusId]);
    }
    public static function changeAssignee(int $taskId, ?int $assigneeId): void {
        Database::call('sp_update_task_assignee', [$taskId, $assigneeId]);
    }

    public static function changeType(int $taskId, int $typeId): void {
        Database::call('sp_update_task_type', [$taskId, $typeId]);
    }

    public static function changePriority(int $taskId, int $priorityId): void {
        Database::call('sp_update_task_priority', [$taskId, $priorityId]);
    }

}

