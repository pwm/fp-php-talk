<?php
declare(strict_types = 1);

namespace F;

use Closure;
use ReflectionFunction;

function curry(callable $fn, ...$args): Closure {
    return function (...$partialArgs) use ($fn, $args) {
        return (function ($args) use ($fn) {
            return count($args) < (new ReflectionFunction($fn))->getNumberOfRequiredParameters()
                ? curry($fn, ...$args)
                : $fn(...$args);
        })(array_merge($args, $partialArgs));
    };
}

// id :: a -> a
$id = function ($x) { return $x; };

// o :: (b -> c) -> (a -> b) -> (a -> c)
$o = curry(function (callable $g, callable $f): Closure {
    return function ($x) use ($g, $f) {
        return $g($f($x));
    };
});

// map :: (a -> b) -> [a] -> [b]
$map = curry('array_map');

// filter :: (a -> bool) -> [a] -> [a]
$filter = curry(function (callable $f, array $a): array {
    return array_filter($a, $f);
});

// foldl :: (a -> b -> a) -> a -> [b] -> a
$foldl = curry(function (callable $f, $acc, $head, ...$tail) {
    return array_reduce(count($tail) > 0
        ? array_merge([$head], $tail)
        : $head,
    $f, $acc);
});

// compose :: [a -> a] -> (a -> a)
$compose = $foldl($o)($id);
