<?php
session_start();

define('HOME', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require_once HOME . 'api/routes/Main.php';
require_once HOME . 'api/config/Config.php';

require_once HOME . 'api/core/Core.php';
require_once HOME . 'api/http/Route.php';

require_once HOME . 'api/helper/Helpers.php';

try {
    Core::dispatch(Route::routes());
} catch (\Throwable $th) {
    Response::json([
        'success' => false,
        'message' => $th->getMessage(),
    ], 400);
}
