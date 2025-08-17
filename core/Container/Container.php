<?php

namespace Core\Container;


class Container
{
    private array $bindings = [];
    private static ?self $instance = null;

    private function __construct() {}

    public static function app(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = [
            'factory' => $factory,
        ];
    }

    public function singleton(string $abstract, callable $factory): void
    {
        // Eager generate singleton
        $concrete = $factory($this);

        // Static factory that returns constant instance
        $this->bind($abstract, fn() => $concrete);
    }

    public function make(string $abstract): object
    {
        if (!isset($this->bindings[$abstract])) {
            throw new \Exception("No binding found for {$abstract}. Please create one in AppServiceProvider.php");
        }

        $binding = $this->bindings[$abstract];

        $factory = $binding['factory'] ?? null;
        if (!is_callable($factory)) {
            throw new \Exception("Binding for {$abstract} is not callable");
        }

        return $factory($this);
    }
}
