<?php
declare(strict_types = 1);
require __DIR__.'/lib.php';

use function F\curry;

// Int -> Int -> Int
$plus = curry(function (int $x, int $y): int { return $x + $y; });

assert($plus(1, 1) === 2);

// [Int] -> Int
$sum = $foldl($plus, 0);

assert($sum([1, 2, 3, 4, 5]) === 15);

// [[Int]] -> Int
$sumMatrix = $compose($sum, $map($sum));

$matrix = [
    [1, 1, 1, 1, 1],
    [2, 2, 2, 2, 2],
    [3, 3, 3, 3, 3],
    [4, 4, 4, 4, 4],
    [5, 5, 5, 5, 5],
];
assert($sumMatrix($matrix) === 75);

// [[Int]] -> [[Int]]
$addOneToMatrix = $map($map($plus(1)));

assert($sumMatrix($addOneToMatrix($matrix)) === 100);
