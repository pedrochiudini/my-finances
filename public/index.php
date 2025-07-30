<?php
session_start();

require_once __DIR__ . "/../api/routes/Main.php";
require_once __DIR__ . '/../api/config/Config.php';

require_once __DIR__ . '/../api/core/Core.php';
require_once __DIR__ . '/../api/http/Route.php';

Core::dispatch(Route::routes());
