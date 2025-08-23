<?php

namespace Core\Support;

class ProviderManager
{
    public function __construct(
        private array $providers = []
    ) {
        // Insert default CoreServiceProvider
        if (!in_array(\Core\Providers\CoreServiceProvider::class, $this->providers)) {
            array_unshift($this->providers, \Core\Providers\CoreServiceProvider::class);
        }
    }

    public static function fromConfig(string $config = "providers")
    {
        $providers = config($config) ?? [];

        foreach ($providers as $provider) {
            if (!class_exists($provider) || !is_subclass_of($provider, \Core\Providers\Provider::class)) {
                throw new \InvalidArgumentException(
                    "Provider {$provider} must be a valid class and extend \\Core\\Providers\\Provider."
                );
            }
        }

        return new self($providers);
    }

    public function load($container)
    {
        $instances = [];
        foreach ($this->providers as $provider) {
            $providerInstance = new $provider($container);
            $instances[$provider] = $providerInstance;
            $providerInstance->register();
        }

        foreach ($this->providers as $provider) {
            $providerInstance = $instances[$provider];
            $providerInstance->boot();
        }
    }
}
