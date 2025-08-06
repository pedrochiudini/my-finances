<?php

require_once HOME . 'api/http/Cors.php';

class Route
{
    public const METHOD_GET    = 'GET';
    public const METHOD_POST   = 'POST';
    public const METHOD_PUT    = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    private static array $routes = [];

    public static function get(string $path, string $action)
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => self::METHOD_GET
        ];
    }

    public static function post(string $path, string $action)
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => self::METHOD_POST
        ];
    }

    public static function put(string $path, string $action)
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => self::METHOD_PUT
        ];
    }

    public static function delete(string $path, string $action)
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => self::METHOD_DELETE
        ];
    }

    public static function routes()
    {
        return self::$routes;
    }
}
