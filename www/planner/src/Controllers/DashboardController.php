<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends BaseController {
    public function index(): void {
        Auth::requireRole('client'); // доступ только зарегистрированным

        $data = Dashboard::tasksByStatus(); // выборка для графика

        $labels = array_column($data, 'status_name');
        $counts = array_column($data, 'task_count');

        $this->render('dashboard', [
            'labels' => json_encode($labels),
            'counts' => json_encode($counts),
        ]);
    }
}
