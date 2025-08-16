<?php

namespace Core\Container;

class Container
{
    private array $bindings = [];

    public static function build(callable $callable): self
    {
        $container = new self();
        $callable($container);
        return $container;
    }

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = [
            'factory' => $factory,
        ];
    }

    public function singleton(string $abstract, object $concrete): void
    {
        $this->bind($abstract, fn() => $concrete);
    }

    public function make(string $abstract): object
    {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding found for {$abstract}");
        }

        $binding = $this->bindings[$abstract];

        $factory = $binding['factory'] ?? null;
        if (!is_callable($factory)) {
            throw new \Exception("Binding for {$abstract} is not callable");
        }

        return $factory($this);
    }
}