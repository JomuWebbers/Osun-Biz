<?php

declare(strict_types=1);

namespace App;

/**
 * Lightweight front-controller / application skeleton.
 * Holds the bootstrap container and dispatches routes.
 */
class Application
{
    public Bootstrap $bootstrap;
    public array $routes = [];

    public function __construct(public string $basePath)
    {
        $this->bootstrap = new Bootstrap();
    }

    public function db(): \PDO
    {
        return $this->bootstrap->db();
    }

    public function config(): Config
    {
        return $this->bootstrap->config;
    }

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        $handler = $this->routes[$method][$uri] ?? null;

        if ($handler === null) {
            http_response_code(404);
            require $this->basePath . '/public/404.php';
            return;
        }

        $handler();
    }
}