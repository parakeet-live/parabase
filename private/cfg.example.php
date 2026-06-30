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

    "auth" => [
        // names of the cookies that have a session key
        "cookies" => ["SITENAMEAUTH"],
        "timeout" => 86400 * 30,
    ],

]);
