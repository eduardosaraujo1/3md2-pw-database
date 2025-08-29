<?php

// TODO: mover isso para um arquivo de configuração, chamado pelo Application::configure()->withProviders()->create(); Só o autoload.php fica aqui
define('PROJECT_ROOT', __DIR__);
ini_set('error_log', PROJECT_ROOT . '/errors.log');
require 'core/autoload.php';
require 'core/functions.php';

use Core\Http\Kernel;
use Core\Http\Request;
use Core\Support\ProviderManager;
use Core\Container\Container;

// Configure Service Container
Container::getInstance()->applyProviders(
    ProviderManager::fromConfig()->getProviders()
);

// Router
require 'routes/web.php';

// Request
$request = app()->make(Request::class);

// Kernel
$kernel = app()->make(Kernel::class);

// Respond
$response = $kernel->handle($request);
$response->send();