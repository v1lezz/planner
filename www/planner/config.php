<?php
return [
    // --- Database config ---
    'db' => [
        'host' => 'database',
        'port' => '3306',
        'name' => 'planner',
        'user' => 'v1lezz',
        'pass' => '1234',
        'charset' => 'utf8mb4',
    ],
    // --- App config ---
    'app' => [
        'base_url' => '', // change if app is under subfolder, e.g., '/jira-mvp/'
        'cookie_secure' => false, // set true if using https
        'cookie_domain' => '',
        'cookie_samesite' => 'Lax',
        'remember_days' => 30,
        'from_email' => 'no-reply@example.com',
        'from_name'  => 'Task Planner',
    ],
];
