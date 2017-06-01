<?php
require __DIR__.'/lib.php';

// Int -> Int -> Int
$plus = curry(function ($x, $y): int { return $x + $y; });

assert($plus(1)(1) === 2);

// [Int] -> Int
$sum = $foldr($plus)(0);

assert($sum([1, 2, 3, 4, 5]) === 15);

// [[Int]] -> Int
$sumMatrix = $compose([$sum, $map($sum)]);

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
