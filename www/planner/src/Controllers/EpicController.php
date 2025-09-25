<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\Board;
use App\Models\Epic;
use App\Models\RefData;

class EpicController extends BaseController
{
    public function createForm(): void {
        Auth::requireRole('client');
        $boardId = (int)($_GET['board_id'] ?? 0);
        $board   = Board::find($boardId);
        if (!$board) { http_response_code(404); echo "Board not found"; return; }

        $members  = Board::members($boardId);      // Owner dropdown
        $statuses = RefData::getStatuses();        // Epic status

        $this->render('epic_form', compact('board','members','statuses'));
    }

    public function create(): void {
        Auth::requireRole('client');
        $boardId = (int)($_POST['board_id'] ?? 0);
        $title   = trim($_POST['title'] ?? '');
        $desc    = trim($_POST['description'] ?? '');
        $ownerId = ($_POST['owner_user_id'] ?? '') !== '' ? (int)$_POST['owner_user_id'] : null;
        $status  = (int)($_POST['status_id'] ?? 1);

        if (!$boardId || !$title) {
            $this->render('epic_form', [
                'error'   => 'Please fill required fields',
                'board'   => Board::find($boardId),
                'members' => Board::members($boardId),
                'statuses'=> RefData::getStatuses(),
            ]);
            return;
        }

        $id = Epic::create($boardId, $title, $desc, $ownerId, $status);
        if ($id) {
            $this->redirect('/board?id='.$boardId);
        }

        $this->render('epic_form', [
            'error'   => 'Failed to create epic',
            'board'   => Board::find($boardId),
            'members' => Board::members($boardId),
            'statuses'=> RefData::getStatuses(),
        ]);
    }
}
