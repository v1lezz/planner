<?php
namespace App\Core;

class Router {
    private array $routes = ['GET'=>[], 'POST'=>[]];
    private array $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function get(string $path, $handler): void { $this->routes['GET'][$path] = $handler; }
    public function post(string $path, $handler): void { $this->routes['POST'][$path] = $handler; }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = rtrim($this->config['app']['base_url'], '/');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
            if ($uri === '') { $uri = '/'; }
        }
        $handler = $this->routes[$method][$uri] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo "Not found";
            return;
        }
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $action] = explode('@', $handler, 2);
            $controller = new $class($this->config);
            $controller->$action();
        } elseif (is_callable($handler)) {
            $handler();
        }
    }
}
