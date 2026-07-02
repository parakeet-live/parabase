<?php

// copy this to cfg.php
define('CONFIG', [

    "site" => [
        // should be something you display in the <title> and used to identify the site by name
        // so you dont have a bunch of strings with a hardcoded name
        "name" => "My App",
        // needed to set cookies properly
        "host" => "example.com",
    ],

    "database" => [
        "host"     => "localhost",
        "name"     => "mydb",
        "username" => "myuser",
        "password" => "secret",
    ],

    "redis" => [
       "enabled"  => false,
       "host"     => "127.0.0.1",
       "port"     => 6379,
       "password" => null,
    ],

    "auth" => [
        // names of the cookies that have a session key
        "cookies" => ["SITENAMEAUTH"],
        "timeout" => 86400 * 30,
    ],

]);
