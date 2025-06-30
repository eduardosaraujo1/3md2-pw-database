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
    }

    public static function create(): void
    {
        require __DIR__ . '/../config.php';

        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        $instance = self::$_instance;
        $host = $CONFIG["host"];
        $username = $CONFIG["username"];
        $password = $CONFIG["password"];
        $database = $CONFIG["database"];
        $port = $CONFIG["port"];

        try {
            $conn = new PDO("mysql:host=$host;dbname=$database;port=$port", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $instance->pdo = $conn;
        } catch (PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function pdo(): PDO
    {
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

        $smt = $pdo->prepare($query);

        if (!$smt) {
            throw new \Exception("Statement create error: prepare command failed");

        }

        if (!$smt->execute($params)) {
            throw new \Exception("Statement create error: execute command failed");
        }

        return $smt->fetchAll();
    }
}