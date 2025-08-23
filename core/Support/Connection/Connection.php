<?php

namespace Core\Support\Connection;

interface Connection
{
    public function getPdo(): \PDO;
}