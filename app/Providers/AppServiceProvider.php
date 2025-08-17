<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\ImageStorageService;
use App\Services\UserService;
use Core\Providers\Provider;
use Core\Services\Session;
use Core\Services\Database;
use Core\Services\Storage;
use Core\Container\Container;
use App\Controllers\AuthController;
use App\Controllers\UserController;

class AppServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(ImageStorageService::class, function (Container $container) {
            $storage = $container->make(Storage::class);
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $oneHundredMB = 100 * 1024; // in kilobytes

            return new ImageStorageService(
                storage: $storage,
                allowedTypes: $allowedTypes,
                maxFileSize: $oneHundredMB,
            );
        });

        $this->app->singleton(UserRepository::class, function (Container $container) {
            $database = $container->make(Database::class);

            return new UserRepository(databaseService: $database);
        });

        $this->app->singleton(AuthService::class, function (Container $container) {
            $userRepository = $container->make(UserRepository::class);
            $session = $container->make(Session::class);
            return new AuthService(
                userRepository: $userRepository,
                sessionService: $session,
            );
        });

        $this->app->singleton(UserService::class, function (Container $container) {
            $userRepository = $container->make(UserRepository::class);
            $session = $container->make(Session::class);
            $imageStorageService = $container->make(ImageStorageService::class);

            return new UserService(
                userRepository: $userRepository,
                sessionService: $session,
                imageStorageService: $imageStorageService
            );
        });

        $this->app->singleton(AuthController::class, function (Container $container) {
            $authService = $container->make(AuthService::class);
            return new AuthController(authService: $authService);
        });

        $this->app->singleton(UserController::class, function (Container $container) {
            $userService = $container->make(UserService::class);
            $authService = $container->make(AuthService::class);
            return new UserController(
                userService: $userService,
                authService: $authService
            );
        });
    }

    public function boot()
    {
        // Bootstrap any application services here.
    }
}
