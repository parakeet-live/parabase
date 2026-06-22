<?php

namespace parabase;

class Sessions
{
    public static function New(int $userId): bool|string
    {
        $key  = bin2hex(random_bytes(128));
        $csrf = bin2hex(random_bytes(32));

        Database::singleton()->run(
            "INSERT INTO sessions (session_key, user_id, created_at, is_valid, csrf)
             VALUES (:key, :uid, UNIX_TIMESTAMP(), 1, :csrf)",
            [":key" => $key, ":uid" => $userId, ":csrf" => $csrf]
        );

        foreach (CONFIG["auth"]["cookies"] as $cookie) {
            setcookie($cookie, $key, [
                'expires'  => time() + (86400 * 30),
                'path'     => '/',
                // adding a dot to the beginning makes the cookie work on subdomains as well
                'domain'   => "." . CONFIG["site"]["host"],
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        return $key;
    }

    public static function Validate(string $key): bool|object
    {
        return Database::singleton()
            ->run(
                "SELECT * FROM sessions WHERE session_key = :key AND is_valid = 1",
                [":key" => $key]
            )
            ->fetch(\PDO::FETCH_OBJ);
    }

    public static function Destroy(string $key): void
    {
        Database::singleton()->run(
            "UPDATE sessions SET is_valid = 0 WHERE session_key = :key",
            [":key" => $key]
        );
    }

    public static function ClearAll(int $userId): void
    {
        Database::singleton()->run(
            "UPDATE sessions SET is_valid = 0 WHERE user_id = :uid",
            [":uid" => $userId]
        );
    }
}
