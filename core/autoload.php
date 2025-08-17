<?php
spl_autoload_register(function ($class) {
    $psr4 = [
        "App\\" => "app/",
        "Core\\" => "core/",
    ];

    foreach ($psr4 as $prefix => $baseDir) {
        // Verificar se a classe pertence ao namespace
        if (strpos($class, $prefix) !== 0) {
            continue;
        }

        // $relativeClass formato: Controllers\AuthController
        $relativeClass = substr($class, strlen($prefix));
        $relativePath = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        $file = PROJECT_ROOT . "/$relativePath";

        if (file_exists($file)) {
            require_once $file;
        }
        return;
    }
});
