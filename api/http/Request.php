<?php

require_once HOME . 'api/http/Route.php';

class Request
{
    public static function getMethod(): string
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function body(): array
    {
        if (!in_array(self::getMethod(), [
            Route::METHOD_POST,
            Route::METHOD_PUT,
            Route::METHOD_DELETE,
        ])) return [];

        $json = file_get_contents('php://input');

        if (!$json) return [];

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) return [];

        return $data;
    }

    public static function authorization(): array
    {
        $authorization = getallheaders();

        if (!isset($authorization['Authorization'])) throw new \Exception("Autorização não encontrada.", 401);

        $authorization_partials = explode(' ', $authorization['Authorization']);

        if (count($authorization_partials) != 2) throw new \Exception("Autorização inválida.", 401);

        return ['authorization' => $authorization_partials[1] ?? ''];
    }
}
