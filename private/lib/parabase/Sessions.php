<?php
namespace parabase;
use parabase\Database;

class Sessions {
    public static function New(int $id) {
        $key = bin2hex(random_bytes(256));
        $csrf = bin2hex(random_bytes(256));
        try {
            Database::singleton()->run(
                "INSERT INTO `sessions` 
                (`key`, `user_id`, `csrf`) 
                VALUES (:k, :uid, :c)",
                [
                    ":k" => $key,
                    ":uid" => $id,
                    ":c" => $csrf
                ]);
            
            foreach (\CONFIG["auth"]["cookies"] as $cookie) {
                setcookie($cookie, $key, [
                    'expires' => time() + \CONFIG["auth"]["timeout"],
                    'path' => '/',
                    'domain' => "." . \CONFIG["site"]["host"],
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            }
        } catch (\Exception $e) {
            error_log("Erm... Error in the Sessions.php script! The following failed: " . $e);
        }
    }

    public static function Validate(string $key): bool | object {
        return Database::singleton()->run("SELECT * FROM `sessions` WHERE revoked = 0 AND `key` = :k", [":k" => $key])->fetch(\PDO::FETCH_OBJ);
    }

    public static function Revoke(string $key) {
        Database::singleton()->run("UPDATE `sessions` SET revoked = 1 WHERE `key` = :k", [":k" => $key]);
    }

    /**
     * $identifier - if it's a string, look up by token, if it's by int, look up by user.
     */
    public static function Vanish(string|int $identifier) {
        if (is_int($identifier)) {
            Database::singleton()->run("DELETE FROM `sessions` WHERE `user_id` = :uid", [":uid" => $identifier]);
        } else if (is_string($identifier)) {
            $q = Database::singleton()->run("SELECT user_id FROM `sessions` WHERE `key` = :k", [":k" => $identifier])->fetch(\PDO::FETCH_OBJ);
            if ($q && isset($q->user_id)) {
                Database::singleton()->run("DELETE FROM `sessions` WHERE `user_id` = :uid", [":uid" => $q->user_id]);
            }
        }
    }
}
