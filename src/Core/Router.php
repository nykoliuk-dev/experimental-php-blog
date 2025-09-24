<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            // Простейшая подстановка {param}
            $regex = '#^' . preg_replace('#\{(\w+)}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                // Оставляем только именованные параметры
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                echo $handler($params);
                return;
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }
}