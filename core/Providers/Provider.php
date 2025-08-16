<?php

namespace Core\Providers;

use Closure;

class Provider
{
    private static self $_self;
    private array $entries = [];

    private function __construct()
    {
    }

    private static function instance(): self
    {
        if (!isset(self::$_self)) {
            self::$_self = new self();
        }

        return self::$_self;
    }

    public static function get(string $class): mixed
    {
        $self = self::instance();

        if (!isset($self->entries[$class])) {
            throw new \RuntimeException("No provider registered for class: {$class}");
        }

        return $self->entries[$class]->resolve();
    }

    public static function registerFactory(string $class, Closure $factory): void
    {
        $self = self::instance();
        $self->entries[$class] = new FactoryProviderEntry($class, $factory);
    }

    public static function registerSingleton(string $class, mixed $instance): void
    {
        $self = self::instance();
        $self->entries[$class] = new SingletonProviderEntry($class, $instance);
    }
}

interface ProviderEntry
{
    public function getType(): ProviderEntryType;
    public function resolve(): mixed;
    public function getClass(): string;
}

class SingletonProviderEntry implements ProviderEntry
{
    private string $class;
    private mixed $instance;

    public function __construct(string $class, mixed $instance)
    {
        $this->class = $class;
        $this->instance = $instance;
    }

    public function getType(): ProviderEntryType
    {
        return ProviderEntryType::Singleton;
    }

    public function resolve(): mixed
    {
        return $this->instance;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}

class FactoryProviderEntry implements ProviderEntry
{
    private string $class;
    private Closure $factory;

    public function __construct(string $class, Closure $factory)
    {
        $this->class = $class;
        $this->factory = $factory;
    }

    public function getType(): ProviderEntryType
    {
        return ProviderEntryType::Factory;
    }

    public function resolve(): mixed
    {
        return ($this->factory)();
    }

    public function getClass(): string
    {
        return $this->class;
    }
}

enum ProviderEntryType
{
    case Factory;
    case Singleton;
}