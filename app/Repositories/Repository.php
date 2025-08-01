<?php

namespace App\Repositories;

use App\Helpers\Response;
use App\Models\User;
use App\Services\DatabaseService;

interface Repository
{
    public string $table { get; }
}