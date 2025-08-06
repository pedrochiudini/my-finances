<?php

require_once HOME . 'api/http/Cors.php';

class Response
{
    public static function json($data = [], int $status = 200): void
    {
        http_response_code($status);

        header("Content-Type: application/json");

        echo json_encode($data);

        exit;
    }
}