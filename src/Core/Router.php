<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function register(array $routes): void
    {
        foreach ($routes as [$method, $path, $handler]) {
            $this->add($method, $path, $handler);
        }
    }

    public function registerAttributeRoutes(array $controllerClasses): void
    {
        foreach ($controllerClasses as $className) {
            $reflection = new ReflectionClass($className);
            foreach ($reflection->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();
                    $this->add($route->method, $route->path, [$className, $method->getName()]);
                }
            }
        }
    }

    public function dispatch(Request $request): mixed
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';
            if (!preg_match($pattern, $request->path(), $matches)) {
                continue;
            }

            $params = array_filter($matches, static fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
            [$class, $action] = $route['handler'];
            $controller = Container::get($class);
            return $controller->{$action}($request, ...array_values($params));
        }

        http_response_code(404);
        echo '404 Not Found';
        return null;
    }
}
