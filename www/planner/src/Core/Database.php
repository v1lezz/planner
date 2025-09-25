<?php
namespace App\Core;
use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    public static function init(array $cfg): void {
        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}";
        self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    public static function pdo(): PDO {
        if (!self::$pdo) throw new \RuntimeException("DB not initialized");
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): \PDOStatement {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function call(string $procedure, array $params = []): \PDOStatement {
        $placeholders = implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedure}($placeholders)";
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute(array_values($params));
        return $stmt;
    }
}
