<?php

namespace parabase;

use parabase\Database;

final class Sessions
{
    private const TTL_CACHE = 300;

    public static function create(int $userId): bool|string {
        $key  = bin2hex(random_bytes(32));
        $csrf = bin2hex(random_bytes(32));
        $ttl  = \CONFIG["auth"]["timeout"];

        try {
            Database::singleton()->run(
                "INSERT INTO `sessions` (`key`, `user_id`, `csrf`, `expires`)
                 VALUES (:k, :uid, :c, :exp)",
                [":k" => $key, ":uid" => $userId, ":c" => $csrf, ":exp" => time() + $ttl]
            );

            self::setCookies($key);
            return $key;
        } catch (\Throwable $e) {
            error_log("Failed to create session: ".$e->getMessage());
            return false;
        }
    }

    public static function validate(string $key): ?Session {
        $cache    = Cache::redis();
        $cacheKey = self::cacheKey($key);
        $session  = null;

        if ($cache) {
            $cached = $cache->get($cacheKey);
            if ($cached !== false) {
                // "0" is negative cache
                if ($cached === "0") return null;
                $session = unserialize($cached, ["allowed_classes" => [Session::class]]);
            }
        }

        if ($session === null) {
            $row = Database::singleton()->run(
                "SELECT `key`, `user_id`, `csrf`, `revoked`, `expires`
                 FROM `sessions` WHERE `revoked` = 0 AND `key` = :k",
                [":k" => $key]
            )->fetch(\PDO::FETCH_OBJ);

            $session = $row ? Session::fromRow($row) : null;

            if ($cache) {
                $cache->setex($cacheKey, self::TTL_CACHE, $session ? serialize($session) : "0");
            }
        }

        if ($session === null) return null;

        if ($session->isExpired()) {
            self::revoke($session->key);
            return null;
        }

        return $session;
    }

    public static function revoke(string $key): void {
        Database::singleton()->run(
            "UPDATE `sessions` SET `revoked` = 1 WHERE `key` = :k",
            [":k" => $key]
        );
        self::forget($key);
    }

    /**
     * revoke every session belonging to a user.
     * $identifier - int is a user id, string is a token we resolve to its user
     */
    public static function revokeAll(int|string $identifier): void {
        $db = Database::singleton();

        if (is_string($identifier)) {
            $row = $db->run(
                "SELECT `user_id` FROM `sessions` WHERE `key` = :k",
                [":k" => $identifier]
            )->fetch(\PDO::FETCH_OBJ);

            if (!$row) return;
            $uid = (int)$row->user_id;
        } else {
            $uid = $identifier;
        }

        $keys = $db->run(
            "SELECT `key` FROM `sessions` WHERE `user_id` = :uid AND `revoked` = 0",
            [":uid" => $uid]
        )->fetchAll(\PDO::FETCH_COLUMN);

        $db->run("UPDATE `sessions` SET `revoked` = 1 WHERE `user_id` = :uid", [":uid" => $uid]);

        foreach ($keys as $k) {
            self::forget($k);
        }
    }

    public static function gc(): int {
        return Database::singleton()->run(
            "DELETE FROM `sessions` WHERE `expires` <= :now",
            [":now" => time()]
        )->rowCount();
    }

    private static function cacheKey(string $key): string {
        return "sess:$key";
    }

    private static function forget(string $key): void {
        if ($cache = Cache::redis()) {
            $cache->del(self::cacheKey($key));
        }
    }

    private static function setCookies(string $key): void {
        foreach (CONFIG["auth"]["cookies"] as $cookie) {
            setcookie($cookie, $key, [
                "expires"  => time() + CONFIG["auth"]["timeout"],
                "path"     => "/",
                "domain"   => ".".CONFIG["site"]["host"],
                "secure"   => true,
                "httponly" => true,
                "samesite" => "Strict",
            ]);
        }
    }
}
