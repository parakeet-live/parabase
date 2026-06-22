<?php

use parabase\ExampleClass;

header('Content-Type: application/json');

$greet = ExampleClass::greet("world");
$sum   = ExampleClass::add(3, 4);

// "my name is parabase"
$obj     = new ExampleClass("parabase");
$message = $obj->message();

echo json_encode([
    "success" => true,
    "data" => [
        "greet" => $greet,
        "sum" => $sum,
        "message" => $message,
    ],
]);
