<?php

namespace parabase;

class Auth
{
    public static function requireAuth(): void
    {
        if (!SESSION) {
            header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }

    public static function requireNoAuth(): void
    {
        if (SESSION) {
            header("Location: /");
            exit();
        }
    }
}
