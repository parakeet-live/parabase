<?php
require( __DIR__ . "/private/cfg.php");
require(__DIR__ . '/vendor/autoload.php');

function str_contains_any(string $haystack, array $needles): bool
{
    return array_reduce($needles, fn($a, $n) => $a || str_contains($haystack, $n), false);
}

function generateUUID(): string
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        if (is_file(__DIR__ . '/500.php')) {
            include __DIR__ . '/500.php';
        } else {
            echo "Internal Server Error";
        }
        exit();
    }
});

use parabase\Sessions;
use parabase\Session;
use parabase\User;

try {
	$conn = new \PDO(
		"mysql:host=" . CONFIG['database']['host'] . ";  
		dbname=" . CONFIG['database']['name'], CONFIG['database']['username'], CONFIG['database']['password']
	); 
	global $conn;

	$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	$conn->setAttribute(\PDO::ATTR_PERSISTENT, true);
} catch(Exception $e) {
	exit("Internal Server Error");
}

function undefineSESSION($cookie) {
	setcookie($cookie, "", 1, "/");
	define('SESSION', (object)array());
}

foreach(CONFIG["auth"]["cookies"] as $cookie){
	$val = str_replace(".", "_", $cookie);
	if(isset($_COOKIE[$val])){
		$validated = Sessions::Validate($_COOKIE[$val]);
		
		// i did this change cuz of nesting issues :P

		if(!$validated) {
			undefineSESSION($cookie);
			break;
		}

		$user = User::fromID($validated->user_id);

		if (!$user) {
			undefineSESSION($cookie);
			break;
		}

		if(!defined('SESSION')) {
			define('SESSION', new Session($validated->session_key, $user, $validated->csrf));
		}
	} 
}

if(!defined('SESSION')){
	define('SESSION', false);
}

require_once(__DIR__ . "/router.php");
exit();