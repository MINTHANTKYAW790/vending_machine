<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    private static array $instances = [];
    private static array $bindings = [];

    public static function bind(string $abstract, string $concrete): void
    {
        self::$bindings[$abstract] = $concrete;
    }

    public static function set(string $id, object $instance): void
    {
        self::$instances[$id] = $instance;
    }

    public static function get(string $id): object
    {
        $id = self::$bindings[$id] ?? $id;

        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        if (!class_exists($id)) {
            throw new RuntimeException("Class {$id} not found.");
        }

        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            $instance = new $id();
            self::$instances[$id] = $instance;
            return $instance;
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException("Cannot resolve dependency for {$id}.");
            }
            $dependencies[] = self::get($type->getName());
        }

        $instance = $reflection->newInstanceArgs($dependencies);
        self::$instances[$id] = $instance;
        return $instance;
    }
}
