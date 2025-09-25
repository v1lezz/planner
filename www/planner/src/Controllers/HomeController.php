<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Models\Board;

class HomeController extends BaseController {
    public function index(): void {
        $user = Auth::user();
        $boards = Board::allForUser($user ? (int)$user['id'] : null);
        $this->render('home', ['boards' => $boards, 'user' => $user]);
    }
}
