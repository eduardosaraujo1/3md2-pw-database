<?php

namespace Core\Support\Connection;

class MySQLConnection implements Connection
{
    private $pdo;

    public function __construct(string $host, string $username, string $password, string $database, int $port, string $migrateFile)
    {
        if (empty($database)) {
            throw new \InvalidArgumentException("Database name cannot be empty.");
        }

        if (!file_exists($migrateFile)) {
            throw new \InvalidArgumentException("Migration file not found at: {$migrateFile}");
        }

        $dsn = "mysql:host=$host;port=$port";
        $this->pdo = new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Verificar se o banco de dados existe
        $stmt = $this->pdo->query("SHOW DATABASES LIKE " . $this->pdo->quote($database));
        $db_exists = $stmt->fetchColumn();

        if (!$db_exists) {
            // Criar o SCHEMA (DATABASE) se não existir
            $this->pdo->exec("DROP SCHEMA IF EXISTS `$database`;");
            $this->pdo->exec("CREATE SCHEMA IF NOT EXISTS `$database`;");
            $this->pdo->exec("USE `$database`;");

            // Executar o arquivo de migração
            $sql = file_get_contents($migrateFile);
            $this->pdo->query($sql);
        } else {
            $this->pdo->exec("USE `$database`;");
        }

    }

    public static function fromConfig(array $config): MySQLConnection
    {
        $mysql = $config['mysql'] ?? [];
        $connection = $mysql['connection'] ?? [];
        if (
            !isset(
            $connection['host'],
            $connection['username'],
            $connection['password'],
            $connection['database'],
            $connection['port'],
            $mysql['migration'],
        )
        ) {
            throw new \InvalidArgumentException("Configuration array is missing required keys.");
        }

        $migrateFile = realpath(PROJECT_ROOT . '/' . $mysql['migration']) ?: '';

        return new MySQLConnection(
            host: $connection['host'],
            username: $connection['username'],
            password: $connection['password'],
            database: $connection['database'],
            port: (int) $connection['port'],
            migrateFile: $migrateFile
        );
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}
