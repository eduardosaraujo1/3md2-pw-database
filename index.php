<?php
define('PROJECT_ROOT', __DIR__);
require 'core/autoload.php';
require 'core/functions.php';

use Core\Http\Kernel;
use Core\Http\Request;

// Bootstrap app service providers
app()->bootstrap();

// Router
require 'routes/rest.php';

// Request
$request = app()->make(Request::class);

// Kernel
$kernel = app()->make(Kernel::class);

// Respond
$response = $kernel->handle($request);
$response->send();