<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Models\User;
use App\Models\RefData;

class AdminController extends BaseController {
    public function index(): void {
        Auth::requireRole('admin');
        $this->render('admin/index', []);
    }

    public function users(): void {
        Auth::requireRole('admin');
        $users = User::all();
        $this->render('admin/users', ['users' => $users]);
    }

    public function userCreate(): void {
        Auth::requireRole('admin');
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = (int)($_POST['role'] ?? 2);
        $pass = $_POST['password'] ?? 'changeme';
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        User::create($name, $email, $hash, $role);
        $this->redirect('/admin/users');
    }

    public function userDelete(): void {
        Auth::requireRole('admin');
        $id = (int)($_POST['id'] ?? 0);
        User::delete($id);
        $this->redirect('/admin/users');
    }

    public function userUpdateRole(): void {
        Auth::requireRole('admin');
        $id = (int)($_POST['id'] ?? 0);
        $role = (int)($_POST['role'] ?? 2);
        User::updateRole($id, $role);
        $this->redirect('/admin/users');
    }

    public function reference(): void {
        Auth::requireRole('admin');
        $this->render('admin/reference', [
            'statuses' => RefData::getStatuses(),
            'priorities' => RefData::getPriorities(),
            'types' => RefData::getTypes(),
            'boardRoles' => RefData::getBoardRoles(),
        ]);
    }

    public function referenceAdd(): void {
        Auth::requireRole('admin');
        $table = $_POST['table'] ?? '';
        $name = trim($_POST['name'] ?? '');
        if ($name) RefData::addToRef($table, $name);
        $this->redirect('/admin/reference');
    }

    public function referenceDelete(): void {
        Auth::requireRole('admin');
        $table = $_POST['table'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        RefData::deleteFromRef($table, $id);
        $this->redirect('/admin/reference');
    }
}
