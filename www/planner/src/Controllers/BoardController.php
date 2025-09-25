<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Models\Board;
use App\Models\RefData;
use App\Models\Task;

class BoardController extends BaseController {
    public function index(): void {
        $user = Auth::user();
        $boards = Board::allForUser($user ? (int)$user['id'] : null);
        $this->render('boards', ['boards' => $boards]);
    }

    public function view(): void {
        $id = (int)($_GET['id'] ?? 0);
        $board = Board::find($id);
        if (!$board) { http_response_code(404); echo "Board not found"; return; }
        $statuses = RefData::getStatuses();
        $tasksByStatus = Task::byBoardGroupedByStatus($id);
        $members = \App\Models\Board::members($id);
        $types = RefData::getTypes();
        $priorities = RefData::getPriorities();
        $this->render('board_view', [
            'board' => $board,
            'statuses' => $statuses,
            'tasksByStatus' => $tasksByStatus,
            'members' => $members,
            'types' => $types,
            'priorities' => $priorities,
        ]);
    }


}
