<?php

class DatabaseConfig
{
    private static function host(): string
    {
        return DB_HOST;
    }

    private static function port(): string
    {
        return DB_PORT;
    }

    private static function name(): string
    {
        return DB_NAME;
    }

    public static function username(): string
    {
        return DB_USERNAME;
    }

    public static function password(): string
    {
        return DB_PASSWORD;
    }

    public static function dsn(): string
    {
        $db_host = self::host();
        $db_port = self::port();
        $db_name = self::name();

        $dsn = "pgsql:host=dpg-d29tr2ur433s739sb2f0-a.oregon-postgres.render.com;port=5432;dbname=myfinances_dbrender;";

        return $dsn;
    }

    public static function options(): array
    {
        return [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];
    }
}