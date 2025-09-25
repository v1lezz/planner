<?php
declare(strict_types=1);
session_start();
$config = require __DIR__ . '/../config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\Database;
use App\Core\Auth;

Database::init($config['db']);
Auth::init($config['app']);

$router = new Router($config);

# ---------------- Routes ----------------
$router->get('/', 'App\Controllers\HomeController@index');
$router->get('/login', 'App\Controllers\AuthController@loginForm');
$router->post('/login', 'App\Controllers\AuthController@login');
$router->get('/register', 'App\Controllers\AuthController@registerForm');
$router->post('/register', 'App\Controllers\AuthController@register');
$router->get('/logout', 'App\Controllers\AuthController@logout');

$router->get('/migrate', 'App\Controllers\MigrationController@run'); // admin-only

$router->get('/boards', 'App\Controllers\BoardController@index');
$router->get('/board', 'App\Controllers\BoardController@view'); // ?id=

$router->get('/task/create', 'App\Controllers\TaskController@createForm');
$router->post('/task/create', 'App\Controllers\TaskController@create');
$router->post('/task/change-status', 'App\Controllers\TaskController@changeStatus');
$router->post('/task/bulk', 'App\Controllers\TaskController@bulk');

$router->get('/admin', 'App\Controllers\AdminController@index');
$router->get('/admin/users', 'App\Controllers\AdminController@users');
$router->post('/admin/users/create', 'App\Controllers\AdminController@userCreate');
$router->post('/admin/users/delete', 'App\Controllers\AdminController@userDelete');
$router->post('/admin/users/update-role', 'App\Controllers\AdminController@userUpdateRole');

$router->get('/admin/reference', 'App\Controllers\AdminController@reference');
$router->post('/admin/reference/add', 'App\Controllers\AdminController@referenceAdd');
$router->post('/admin/reference/delete', 'App\Controllers\AdminController@referenceDelete');

$router->get('/admin/boards', 'App\Controllers\AdminBoardController@index');
$router->post('/admin/boards/create', 'App\Controllers\AdminBoardController@create');
$router->post('/admin/boards/delete', 'App\Controllers\AdminBoardController@delete');

$router->get('/admin/boards/edit', 'App\Controllers\AdminBoardController@editForm'); // ?id=
$router->post('/admin/boards/update', 'App\Controllers\AdminBoardController@update');

$router->post('/admin/boards/member/add', 'App\Controllers\AdminBoardController@memberAdd');
$router->post('/admin/boards/member/remove', 'App\Controllers\AdminBoardController@memberRemove');

$router->get('/epic/create', 'App\Controllers\EpicController@createForm');
$router->post('/epic/create', 'App\Controllers\EpicController@create');
$router->post('/task/change-assignee', 'App\Controllers\TaskController@changeAssignee');
$router->post('/task/change-type', 'App\Controllers\TaskController@changeType');
$router->post('/task/change-priority', 'App\Controllers\TaskController@changePriority');
$router->post('/admin/boards/epic/update', 'App\Controllers\AdminBoardController@epicUpdate');
$router->post('/admin/boards/epic/delete', 'App\Controllers\AdminBoardController@epicDelete');
$router->get('/dashboard', 'App\Controllers\DashboardController@index');


$router->dispatch();
