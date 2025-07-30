<?php

namespace app\infrastructure\database;

use app\application\http\Response;
use PDO;
use PDOException;

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
            } catch (PDOException $e) {
                Response::json([
                    'error'   => true,
                    'success' => false,
                    'message' => 'Erro ao conectar ao banco de dados.',
                    'details' => $e->getMessage()
                ], 500);
            }
        }

        return self::$pdo;
    }
}