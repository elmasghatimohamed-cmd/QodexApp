<?php

namespace App\Core;

use Closure;

class Router
{
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, Closure $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    public function map(string $method, string $path, Closure $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }
    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        if (isset($this->routes[$method][$path])) {
            return ($this->routes[$method][$path])();
        }

        http_response_code(404);
        echo "Not Found";
        return null;
    }
}

