<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Board;
use App\Models\User;
use App\Models\RefData;

class AdminBoardController extends BaseController
{
    public function index(): void {
        Auth::requireRole('admin');
        $boards = Board::all();
        $this->render('admin/boards', ['boards' => $boards]);
    }

    public function create(): void {
        Auth::requireRole('admin');
        $name = trim($_POST['name'] ?? '');
        $key  = strtoupper(trim($_POST['board_key'] ?? ''));
        if ($name && $key) Board::create($name, $key);
        $this->redirect('/admin/boards');
    }

    public function delete(): void {
        Auth::requireRole('admin');
        $id = (int)($_POST['id'] ?? 0);
        if ($id) Board::deleteCascade($id);
        $this->redirect('/admin/boards');
    }

    public function editForm(): void {
        Auth::requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $board = \App\Models\Board::find($id);
        if (!$board) { http_response_code(404); echo "Board not found"; return; }

        $members     = \App\Models\Board::members($id);
        $users       = \App\Models\User::all();
        $boardRoles  = \App\Models\RefData::getBoardRoles();
        $memberIds   = array_column($members, 'user_id');
        $candidates  = array_values(array_filter($users, fn($u)=>!in_array($u['id'], $memberIds, true)));

        $statuses    = \App\Models\RefData::getStatuses();
        $epics       = \App\Models\Epic::listByBoardDetailed($id); // ← новые данные

        $this->render('admin/board_edit', compact('board','members','candidates','boardRoles','statuses','epics'));
    }


    public function update(): void {
        Auth::requireRole('admin');
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $key  = strtoupper(trim($_POST['board_key'] ?? ''));
        if ($id && $name && $key) Board::update($id, $name, $key);
        $this->redirect('/admin/boards/edit?id=' . $id);
    }

    public function memberAdd(): void {
        Auth::requireRole('admin');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $userId  = (int)($_POST['user_id'] ?? 0);
        $roleId  = (int)($_POST['role_id'] ?? 1);
        if ($boardId && $userId) Board::setMember($userId, $boardId, $roleId);
        $this->redirect('/admin/boards/edit?id=' . $boardId);
    }

    public function memberRemove(): void {
        Auth::requireRole('admin');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $userId  = (int)($_POST['user_id'] ?? 0);
        if ($boardId && $userId) Board::removeMember($userId, $boardId);
        $this->redirect('/admin/boards/edit?id=' . $boardId);
    }

    public function epicUpdate(): void {
        Auth::requireRole('admin');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $id      = (int)($_POST['id'] ?? 0);
        $title   = trim($_POST['title'] ?? '');
        $status  = (int)($_POST['status_id'] ?? 1);
        $ownerId = ($_POST['owner_user_id'] ?? '') !== '' ? (int)$_POST['owner_user_id'] : null;

        if ($boardId && $id && $title) {
            \App\Models\Epic::update($id, $title, $_POST['description'] ?? null, $ownerId, $status);
        }
        $this->redirect('/admin/boards/edit?id=' . $boardId);
    }

    public function epicDelete(): void {
        Auth::requireRole('admin');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $id      = (int)($_POST['id'] ?? 0);
        if ($id) {
            \App\Models\Epic::deleteCascade($id);
        }
        $this->redirect('/admin/boards/edit?id=' . $boardId);
    }


}
