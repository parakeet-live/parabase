<?php

use parabase\ExampleClass;

$greeting = ExampleClass::greet("visitor");
$sum      = ExampleClass::add(10, 32);

$obj = new ExampleClass("parabase");

// TO BE CLEAR you do not have to do this ugly shit
// just make a different class that handles this "template" for you
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= CONFIG["site"]["name"] ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($greeting) ?></h1>
    <p><?= htmlspecialchars($obj->message()) ?></p>
    <p>ExampleClass::add(10, 32) = <?= $sum ?></p>
    <hr>
    <p>api demo: <a href="/api/example">/api/example</a></p>
</body>
</html>
