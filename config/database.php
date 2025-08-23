<?php
return [
    'default' => 'mysql',
    'mysql' => [
        'connection' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => 'pw_database',
            'port' => 3306,
        ],
        'migration' => 'database/migrations/mysql.sql',
    ],
    'sqlite' => [
        'file' => 'database/database.sqlite',
        'migration' => 'database/migrations/sqlite.sql',
    ],
];