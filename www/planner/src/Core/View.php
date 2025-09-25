<?php
namespace App\Core;

class View {
    public static function render(string $template, array $vars = []): void {
        extract($vars);
        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';
        require __DIR__ . '/../../templates/_layout.php';
    }
}
