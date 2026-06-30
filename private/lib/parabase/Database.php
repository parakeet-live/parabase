<?php

namespace parabase;

use PDO;
use PDOStatement;

final class Database
{
    private static ?Database $instance = null;

    private static string $dsn;
    private static string $username;
    private static string $password;

    private ?PDO $pdo = null;

    private function __construct() {
        $c = \CONFIG["database"];
        self::$dsn = "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4";
        self::$username = $c["username"];
        self::$password = $c["password"];
    }

    public static function singleton(): Database {
        return self::$instance ??= new self();
    }

    private function connection(): PDO {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        return $this->pdo = new PDO(self::$dsn, self::$username, self::$password, [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
        ]);
    }

    public function run(string $sql, array $params = []): PDOStatement {
        $db = $this->connection();

        if ($params === []) {
            return $db->query($sql);
        }

        $stmt = $db->prepare($sql);
        $this->bindAll($stmt, $params);
        $stmt->execute();

        return $stmt;
    }

    private function bindAll(PDOStatement $stmt, array $params): void {
        foreach ($params as $key => $val) {
            $slot = is_int($key) ? $key + 1 : $key;
            $stmt->bindValue($slot, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }

    public function lastInsertId(): string {
        return $this->connection()->lastInsertId();
    }
}
