<?php
require __DIR__.'/lib.php';

$plus = curry(function ($x, $y) { return $x + $y; });

$sum = $foldr($plus)(0);

assert($sum([1, 2, 3, 4, 5]) === 15);

$matrix = [
    [1, 1, 1, 1, 1],
    [2, 2, 2, 2, 2],
    [3, 3, 3, 3, 3],
    [4, 4, 4, 4, 4],
    [5, 5, 5, 5, 5],
];

$sumMatrix = $o($sum)($map($sum));

assert($sumMatrix($matrix) === 75);

$addOneToMatrix = $map($map($plus(1)));

assert($sumMatrix($addOneToMatrix($matrix)) === 100);
