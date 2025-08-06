<?php
try {
    $dsn = "pgsql:host=dpg-d29tr2ur433s739sb2f0-a.oregon-postgres.render.com;port=5432;dbname=myfinances_dbrender;";
    $pdo = new PDO($dsn, "myfinances", "Ka2FbWOSKlHRX7a38WB9vXEFXv5PgaoW");
    echo "Conectado com sucesso!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}