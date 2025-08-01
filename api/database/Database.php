<?php

require_once HOME . 'api/database/DatabaseConfig.php';
require_once HOME . 'api/http/Response.php';

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    DatabaseConfig::dsn(),
                    DatabaseConfig::username(),
                    DatabaseConfig::password(),
                    DatabaseConfig::options()
                );
            } catch (\PDOException $e) {
                throw new \Exception("Erro ao conectar ao banco de dados.", 7500, $e);
            }
        }

        return self::$pdo;
    }
}
