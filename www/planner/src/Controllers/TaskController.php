<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Task;
use App\Models\RefData;
use App\Models\Board;
use App\Models\Epic;

class TaskController extends BaseController {
    public function createForm(): void {
        Auth::requireRole('client');
        $boardId = (int)($_GET['board_id'] ?? 0);
        $board = Board::find($boardId);

        $members = $board ? Board::members($boardId) : [];
        $epics = $board ? Epic::listByBoard($boardId) : [];

        $this->render('task_form', [
            'board'      => $board,
            'statuses'   => RefData::getStatuses(),
            'types'      => RefData::getTypes(),
            'priorities' => RefData::getPriorities(),
            'members'    => $members,
            'epics'      => $epics,
        ]);
    }

    public function create(): void {
        Auth::requireRole('client');
        $boardId    = (int)($_POST['board_id'] ?? 0);
        $title      = trim($_POST['title'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $status     = (int)($_POST['status_id'] ?? 1);
        $type       = (int)($_POST['type_id'] ?? 1);
        $priority   = (int)($_POST['priority_id'] ?? 2);
        $epicId     = ($_POST['epic_id'] ?? '') !== '' ? (int)$_POST['epic_id'] : null;
        $assigneeId = ($_POST['assignee_id'] ?? '') !== '' ? (int)$_POST['assignee_id'] : null;
        $due        = $_POST['due_date'] ?? null;
        $sp         = ($_POST['story_points'] ?? '') !== '' ? (int)$_POST['story_points'] : null;
        $notify     = isset($_POST['notify_email']);
        $authorId   = (int)Auth::user()['id'];

        $id = Task::create($boardId, $epicId, $title, $desc, $type, $priority, $status, $authorId, $assigneeId, $due, $sp, $notify);
        if ($id) {
            $this->redirect('/board?id=' . $boardId);
        }
        $this->render('task_form', [
            'error'      => 'Failed to create task',
            'board'      => Board::find($boardId),
            'statuses'   => RefData::getStatuses(),
            'types'      => RefData::getTypes(),
            'priorities' => RefData::getPriorities(),
            'members'    => Board::members($boardId),
            'epics'      => Epic::listByBoard($boardId),
        ]);
    }

    public function changeStatus(): void {
        Auth::requireRole('client');
        $taskId  = (int)($_POST['task_id'] ?? 0);
        $statusId = (int)($_POST['status_id'] ?? 0);
        Task::changeStatus($taskId, $statusId);
        $boardId = (int)($_POST['board_id'] ?? 0);
        $this->redirect('/board?id=' . $boardId);
    }

    public function bulk(): void {
        Auth::requireRole('client');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $ids = $_POST['ids'] ?? [];
        $action = $_POST['action'] ?? '';
        if (!$ids) $this->redirect('/board?id=' . $boardId);
        if ($action === 'delete') {
            Task::bulkDelete($boardId, $ids);
        } elseif ($action === 'status') {
            $newStatus = (int)($_POST['new_status_id'] ?? 1);
            Task::bulkChangeStatus($boardId, $ids, $newStatus);
        }
        $this->redirect('/board?id=' . $boardId);
    }
    public function changeAssignee(): void {
        Auth::requireRole('client');
        $taskId = (int)($_POST['task_id'] ?? 0);
        $assigneeId = ($_POST['assignee_id'] ?? '') !== '' ? (int)$_POST['assignee_id'] : null;
        Task::changeAssignee($taskId, $assigneeId);
        $boardId = (int)($_POST['board_id'] ?? 0);
        $this->redirect('/board?id=' . $boardId);
    }

    public function changeType(): void {
        Auth::requireRole('client');
        $taskId = (int)($_POST['task_id'] ?? 0);
        $typeId = (int)($_POST['type_id'] ?? 0);
        Task::changeType($taskId, $typeId);
        $boardId = (int)($_POST['board_id'] ?? 0);
        $this->redirect('/board?id=' . $boardId);
    }

    public function changePriority(): void {
        Auth::requireRole('client');
        $taskId = (int)($_POST['task_id'] ?? 0);
        $priorityId = (int)($_POST['priority_id'] ?? 0);
        Task::changePriority($taskId, $priorityId);
        $boardId = (int)($_POST['board_id'] ?? 0);
        $this->redirect('/board?id=' . $boardId);
    }

}
