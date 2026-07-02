<?php

namespace parabase;

final class Cache
{
    private static ?\Redis $redis = null;
    private static bool $tried = false;

    public static function redis(): ?\Redis {
        if (self::$tried) {
            return self::$redis;
        }
        self::$tried = true;

        if (CONFIG["redis"]["enabled"] == false || !extension_loaded("redis")) {
            return null;
        }

        try {
            $c = CONFIG["redis"];
            $r = new \Redis();
            $r->connect($c["host"], $c["port"] ?? 6379, 1.0);
            if (!empty($c["password"])) {
                $r->auth($c["password"]);
            }
            return self::$redis = $r;
        } catch (\Throwable $e) {
            error_log("redis failure: ".$e->getMessage());
            return null;
        }
    }
}
