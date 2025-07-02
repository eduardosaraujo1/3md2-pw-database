<?php

namespace Core\Database;

use \PDO;
use \PDOException;

class Connection
{
    private static $_instance;
    public $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/config.php';

        $host = $config["host"];
        $username = $config["username"];
        $password = $config["password"];
        $schema = $config["database"];
        $port = $config["port"];

        try {
            $dsn = "mysql:host=$host;port=$port";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SHOW SCHEMAS LIKE " . $pdo->quote($schema));
            $db_exists = $stmt->fetchColumn();

            if (!$db_exists) {
                $sqlFile = __DIR__ . "/script.sql";
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $pdo->exec($sql);
                }
            }

            $pdo->exec("USE `$schema`");
            $this->pdo = $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function pdo(): PDO
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance->pdo;
    }

    public static function unsafe_query(string $query): bool|\PDOStatement
    {
        $pdo = self::pdo();

        return $pdo->query($query);
    }

    public static function query(string $query, array $params = [])
    {
        $pdo = self::pdo();

        $stmt = $pdo->prepare($query);

        if (!$stmt) {
            throw new \Exception("Statement create error: prepare command failed");
        }

        if (!$stmt->execute($params)) {
            throw new \Exception("Statement create error: execute command failed");
        }

        return $stmt->rowCount();
    }

    public static function fetch(string $query, array $params = [])
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare($query);

        if (!$stmt) {
            throw new \Exception("Statement create error: prepare command failed");

        }

        if (!$stmt->execute($params)) {
            throw new \Exception("Statement create error: execute command failed");
        }

        return $stmt->fetchAll();
    }
}