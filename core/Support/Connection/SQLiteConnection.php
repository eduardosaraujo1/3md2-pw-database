<?php

namespace Core\Support\Connection;

class SQLiteConnection implements Connection
{
    private $pdo;

    public function __construct(string $file, string $migrateFile)
    {
        if (empty($file) || !is_writable(dirname($file))) {
            throw new \InvalidArgumentException("Invalid or unwritable database file path: {$file}");
        }

        if (!file_exists($file)) {
            touch($file); // Criar arquivo do banco se ele não existir
        }

        if (!file_exists($migrateFile)) {
            throw new \InvalidArgumentException("Migration file not found at: {$migrateFile}");
        }

        $dsn = "sqlite:$file";
        $this->pdo = new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Check if the database is already initialized
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tb_contato'");
        $tableExists = $stmt->fetchColumn();

        if (!$tableExists) {
            // Execute migration file to set up the database
            $sql = file_get_contents($migrateFile);
            $this->pdo->exec($sql);
        }
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public static function fromConfig(array $config): SQLiteConnection
    {
        $sqlite = $config['sqlite'] ?? [];
        if (
            !isset(
            $sqlite['file'],
            $sqlite['migration']
        )
        ) {
            throw new \InvalidArgumentException("Configuration array is missing required keys.");
        }

        $file = PROJECT_ROOT . '/' . $sqlite['file'];
        $migrateFile = realpath(PROJECT_ROOT . '/' . $sqlite['migration']) ?: '';

        return new SQLiteConnection(
            file: $file,
            migrateFile: $migrateFile
        );
    }
}
