<?php
namespace App\Controllers;
use App\Core\View;

abstract class BaseController {
    protected array $config;
    public function __construct(array $config) { $this->config = $config; }
    protected function render(string $template, array $vars = []): void {
        View::render($template, $vars);
    }
    protected function redirect(string $path): void {
        header("Location: {$path}");
        exit;
    }
}
