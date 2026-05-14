<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'notas_parciales');
define('DB_USER', 'notas_user');
define('DB_PASS', 'notas2026');

class Database {
    private static ?PDO $connection = null;

    public static function connect(): PDO {
        if (self::$connection === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                DB_HOST, DB_PORT, DB_NAME
            );
            self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$connection;
    }
}
