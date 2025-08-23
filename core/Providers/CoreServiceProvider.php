<?php

namespace Core\Providers;

use Core\Container\Container;
use Core\Support\Connection\Connection;
use Core\Support\Connection\MySQLConnection;
use Core\Support\Connection\SQLiteConnection;
use Core\Http\Kernel;
use Core\Http\Request;
use Core\Http\Response;
use Core\Routing\Router;
use Core\Services\Database;
use Core\Services\Session;
use Core\Services\Storage;

class CoreServiceProvider extends Provider
{
    public function register()
    {
        // Request Lifecycle
        $this->app->singleton(Request::class, function () {
            return Request::createFromGlobals();
        });

        $this->app->singleton(Response::class, function () {
            return new Response();
        });

        $this->app->singleton(Router::class, function () {
            return new Router();
        });

        $this->app->singleton(Kernel::class, function () {
            $router = app()->make(Router::class);

            return new Kernel($router);
        });

        // PHP Session
        $this->app->singleton(Session::class, function () {
            return new Session();
        });

        // Storage
        $this->app->singleton(Storage::class, function () {
            return new Storage();
        });

        // Database
        $this->app->singleton(Connection::class, function () {
            $dbConfig = config('database');
            $dbDriver = $dbConfig['default'];

            $connection = match ($dbDriver) {
                'sqlite' => SQLiteConnection::fromConfig(config: $dbConfig),
                'mysql' => MySQLConnection::fromConfig(config: $dbConfig),
                default => throw new \InvalidArgumentException("Unsupported database driver: {$dbDriver}"),
            };

            return $connection;
        });

        $this->app->singleton(Database::class, function (Container $container) {
            $connection = $container->make(Connection::class);

            return new Database(connection: $connection);
        });
    }

    public function boot()
    {
        //
    }
}