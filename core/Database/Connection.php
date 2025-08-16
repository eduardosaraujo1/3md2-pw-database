<?php

namespace Core\Database;

interface Connection
{
    public function getPdo(): \PDO;
}