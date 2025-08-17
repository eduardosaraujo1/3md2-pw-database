<?php

namespace Core\Container;

use Core\Providers\CoreServiceProvider;


class Container
{
    private array $bindings = [];
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bootstrap()
    {
        // get service provider list from config
        $providers = config("providers") ?? [];
        array_unshift($providers, CoreServiceProvider::class);

        // register all providers
        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }

        // then boot all providers
        foreach ($providers as $provider) {
            $this->bootProvider($provider);
        }
    }

    private function registerProvider(string $provider): void
    {
        if (!class_exists($provider)) {
            throw new \InvalidArgumentException("Provider class '{$provider}' not found.");
        }

        $providerInstance = new $provider($this);
        $providerInstance->register();
    }

    private function bootProvider(string $provider): void
    {
        if (!class_exists($provider)) {
            throw new \InvalidArgumentException("Provider class '{$provider}' not found.");
        }

        $providerInstance = new $provider($this);
        $providerInstance->boot();
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
