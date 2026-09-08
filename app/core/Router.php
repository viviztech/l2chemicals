<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][] = [$path, $handler];
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][] = [$path, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = '/' . trim((string) parse_url($uri, PHP_URL_PATH), '/');
        $path = $path === '//' ? '/' : $path;

        foreach ($this->routes[strtoupper($method)] ?? [] as [$route, $handler]) {
            $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[a-zA-Z0-9-]+)', $route);
            if (!preg_match('#^' . $pattern . '/?$#', $path, $matches)) {
                continue;
            }
            $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            if (is_array($handler) && is_string($handler[0])) {
                $handler[0] = new $handler[0]();
            }
            call_user_func_array($handler, $parameters);
            return;
        }

        http_response_code(404);
        view('errors/404');
    }
}

