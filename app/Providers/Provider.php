<?php

namespace App\Providers;

use Core\Container\Container;

abstract class Provider
{
    public function __construct(
        protected Container $app
    ) {}

    abstract public function register();

    abstract public function boot();
}
