<?php
namespace App\Controllers;
use App\Core\Auth;
use App\Models\User;

class AuthController extends BaseController {
    public function loginForm(): void {
        $this->render('auth_login', []);
    }
    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        if (Auth::login($email, $password, $remember)) {
            $this->redirect('/');
        }
        $this->render('auth_login', ['error' => 'Invalid email or password']);
    }
    public function logout(): void {
        Auth::logout();
        $this->redirect('/');
    }
    public function registerForm(): void {
        $this->render('auth_register', []);
    }
    public function register(): void {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$name || !$email || !$password) {
            $this->render('auth_register', ['error' => 'Fill all fields']);
            return;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id = User::create($name, $email, $hash, 2); // client by default
        if ($id) {
            Auth::login($email, $password, false);
            $this->redirect('/');
        } else {
            $this->render('auth_register', ['error' => 'Email already exists']);
        }
    }
}
