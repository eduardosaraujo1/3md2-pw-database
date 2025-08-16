<?php
namespace Core\Services;

use Core\Database\Connection;

class Database
{
    public $pdo;

    public function __construct(Connection $connection)
    {
        $this->pdo = $connection->getPdo();
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