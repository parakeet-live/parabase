<?php

namespace parabase;

class ExampleClass
{
    public string $name;

    public static function greet(string $name): string
    {
        return "hello, $name!";
    }

    public static function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function message(): string
    {
        return "my name is {$this->name}";
    }
}
