<?php

// Versi server Hostinger (PRD-09): file ini ada di domains/aniwijaya.com/public_html/,
// kode Laravel di domains/aniwijaya.com/pb-ani-wijaya/. Folder publik diatur lewat APP_PUBLIC_PATH di .env.

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../pb-ani-wijaya/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../pb-ani-wijaya/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../pb-ani-wijaya/bootstrap/app.php';

$app->handleRequest(Request::capture());
