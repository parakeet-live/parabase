<?php

// this altorouter file has an included function that should help make it slightly easier
// to define simple routes and makes them more compact in general.
// if you wish, you may also use $router->map().
// please view the bottom of the file to understand how to create routes properly.


// for now, these routes are a basic demonstration of how i organize my routes in my projects.
// you can do it however you'd like, please don't feel confined to my structure.

$router = new AltoRouter();

// -----
// userfacing
// -----

route('GET', '/', '/private/views/index.php');

$router->map('GET', '/content', function() { // this is just using altorouter normally.
    $var = true;
    if($var) {
        require __DIR__.'/private/views/content1.php';
    } else {
        require __DIR__.'/private/views/content2.php';
    }
});

// -----
// api
// -----

route('GET', '/api/example', '/private/api/example/example.php');




$match = $router->match();

function route($method, $path, $file) {
    global $router;
    $router->map($method, $path, function(...$params) use ($file) {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        require __DIR__.$file;
    });
}

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // adjust 404 page here, or just remote/comment out the 404 page
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    require __DIR__.'/private/views/errors/404.php';
    exit();
}

/*
route creation:

I want to create a route!
Use this:
route('GET', '/', '/private/views/file');

I want a parameter in the URL!
Use this:
route('GET', '/stage1/[a:variable]', '/private/views/file');
This can be accessed in the script via $variable.

*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[create|edit:action] // Match either 'create' or 'edit' as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional


I need extra middleware code.
Use this:
$router->map('GET', '/proxy/[**:route]', function($route) {
    if (empty($route) || strlen($route) > 255) {
        http_response_code(500);
        exit();
    }
    require __DIR__.'/private/file';
});*/