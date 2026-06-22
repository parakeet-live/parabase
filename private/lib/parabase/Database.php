<?php

namespace parabase;

/*
 * usage:
 *   Database::singleton()->run("SELECT * FROM posts")->fetchAll(PDO::FETCH_OBJ);
 *   Database::singleton()->run("SELECT * FROM posts WHERE id = :id", [":id" => $id])->fetch(PDO::FETCH_OBJ);
 *   Database::singleton()->run("INSERT INTO posts (title) VALUES (:t)", [":t" => $title]);
 *   Database::singleton()->lastInsertId();
 */
class Database
{
    private static ?self $instance = null;
    public \PDO $pdo;

    public static function singleton(): self
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    function __construct()
    {
        $this->pdo = new \PDO(
            "mysql:host=" . \CONFIG["database"]["host"] . ";dbname=" . \CONFIG["database"]["name"] . ";charset=utf8mb4",
            \CONFIG["database"]["username"],
            \CONFIG["database"]["password"]
        );
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_PERSISTENT, true);
    }

    function run(string $sql, array $args = null): \PDOStatement
    {
        if (!$args) return $this->pdo->query($sql);

        $stmt = $this->pdo->prepare($sql);
        foreach ($args as $param => $value) {
            // pdo positional params are 1-based
            $idx = is_int($param) ? $param + 1 : $param;
            $stmt->bindValue($idx, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }

    function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
