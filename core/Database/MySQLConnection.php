<?php
namespace Core\Database;

use Core\Config\DatabaseConfig;

class MySQLConnection implements Connection
{
    private $pdo;

    public function __construct(DatabaseConfig $config, string $migrateFile)
    {
        $host = $config->host;
        $username = $config->username;
        $password = $config->password;
        $database = $config->database;
        $port = $config->port;

        if (empty($database)) {
            throw new \InvalidArgumentException("Database name cannot be empty.");
        }

        if (!file_exists($migrateFile)) {
            throw new \InvalidArgumentException("Migration file not found at: {$migrateFile}");
        } {
            $dsn = "mysql:host=$host;port=$port";
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $stmt = $this->pdo->query("SHOW DATABASES LIKE " . $this->pdo->quote($database));
            $db_exists = $stmt->fetchColumn();

            if (!$db_exists && file_exists($migrateFile)) {
                $sql = file_get_contents($migrateFile);
                $this->pdo->exec($sql);
            }

            $this->pdo->exec("USE `$database`");
        }
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}