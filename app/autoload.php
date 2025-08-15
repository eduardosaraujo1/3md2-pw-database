<?php
require __DIR__ . '/Controllers/AuthController.php';
require __DIR__ . '/Controllers/UserController.php';
require __DIR__ . '/Helpers/Response.php';
require __DIR__ . '/Helpers/Result.php';
require __DIR__ . '/Models/User.php';
require __DIR__ . '/Repositories/UserRepository.php';
require __DIR__ . '/Services/AuthService.php';
require __DIR__ . '/Services/UserService.php';
require __DIR__ . '/Services/DatabaseService.php';
require __DIR__ . '/Services/SessionService.php';
require __DIR__ . '/Services/ImageStorageService.php';
require __DIR__ . '/DTO/UserRegisterDTO.php';
require __DIR__ . '/DTO/SignInCredentialsDTO.php';
require __DIR__ . '/Providers/Provider.php';