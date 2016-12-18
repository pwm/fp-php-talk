<?php
declare(strict_types=1);

require __DIR__.'/lib.php';

use function F\curry;

// slice :: String -> String -> [String]
$slice = curry('explode');

// concat :: String -> [String] -> String
$concat = curry('implode');

// concat :: String -> String -> Bool
$match = curry('preg_match');

// head :: String -> Char
$head = function (string $s): string { return $s[0]; };

// isWord :: String -> Bool
$isWord = $match('/[a-z]/i');

// initials :: String -> String
$initials = $compose(
    $concat(' '),
    $map($compose('strtoupper', $head)),
    $filter($isWord),
    $slice(' ')
);

assert($initials('This is rather cool :)') === 'T I R C');
