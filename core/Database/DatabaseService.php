<?php
namespace Core\Database;

use PDO;

class DatabaseService
{
    public $pdo;

    public function __construct()
    {
        $this->pdo = self::connect();
    }

    /**
     * Initializes and returns a PDO instance, creates schema if needed.
     */
    public static function connect(): PDO
    {
        $config = require __DIR__ . '/../../config/database.php';

        $host = $config["host"];
        $username = $config["username"];
        $password = $config["password"];
        $schema = $config["database"];
        $port = $config["port"];

        $dsn = "mysql:host=$host;port=$port";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SHOW SCHEMAS LIKE " . $pdo->quote($schema));
        $db_exists = $stmt->fetchColumn();

        if (!$db_exists) {
            $sqlFile = __DIR__ . '/../../script.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);
            }
        }

        $pdo->exec("USE `$schema`");
        return $pdo;
    }

    public function unsafe_query(string $query): bool|\PDOStatement
    {
        return $this->pdo->query($query);
    }

    public function query(string $query, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($query);

        if (!$stmt) {
            throw new \Exception("Statement create error: prepare command failed");
        }

        if (!$stmt->execute($params)) {
            throw new \Exception("Statement create error: execute command failed");
        }

        return $stmt->rowCount() > 0;
    }

    public function fetch(string $query, array $params = [])
    {
        $stmt = $this->pdo->prepare($query);

        if (!$stmt) {
            throw new \Exception("Statement create error: prepare command failed");
        }

        if (!$stmt->execute($params)) {
            throw new \Exception("Statement create error: execute command failed");
        }

        return $stmt->fetchAll();
    }
}