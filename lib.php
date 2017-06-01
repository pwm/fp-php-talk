<?php

// Syntactic sugar so we don't have to manually write curried function definitions.
// We still curry at function calls for the sake of authenticity.
function curry($f, ...$args) {
    return function (...$partialArgs) use ($f, $args) {
        return (function ($args) use ($f) {
            return count($args) < (new ReflectionFunction($f))->getNumberOfRequiredParameters()
                ? curry($f, ...$args)
                : $f(...$args);
        })(array_merge($args, $partialArgs));
    };
}

// The identity function
// a -> a
$id = function ($x) { return $x; };

// Function composition operator aka. little circle
// (b -> c) -> (a -> b) -> (a -> c)
$o = curry(function ($g, $f) {
    return function ($x) use ($g, $f) {
        return $g($f($x));
    };
});

// The Y fixed point combinator in applicative-order form
// It transforms "pseudo recursive" functions to recursive functions
// λf.(λx.f (λv.x x v)) (λx.f (λv.x x v))
$Y = function ($f) {
    return
        (function ($x) use ($f) { return $f(function ($v) use ($x) { return $x($x)($v); }); })
        (function ($x) use ($f) { return $f(function ($v) use ($x) { return $x($x)($v); }); });
};

// Right fold, the "essence" of recursion on lists (made recursive via the Y combinator)
// (a -> b -> b) -> b -> [a] -> b
$foldr = $Y(function ($foldr) {
    return curry(function ($f, $v, $l) use ($foldr) {
        return count($l) > 0
            ? $f(array_shift($l))($foldr($f)($v)($l))
            : $v;
    });
});

// Map, expressed by foldr
// (a -> b) -> [a] -> [b]
$map = curry(function ($f, $l) use ($foldr) {
    return $foldr
    (curry(function ($x, $v) use ($f) { return array_merge([$f($x)], $v); }))
    ([])
    ($l);
});

// Filter, expressed by foldr
// (a -> Bool) -> [a] -> [a]
$filter = curry(function ($p, $l) use ($foldr) {
    return $foldr
    (curry(function ($x, $v) use ($p) { return $p($x) ? array_merge([$x], $v) : $v; }))
    ([])
    ($l);
});

// Composition of a list of functions
// compose :: [a -> a] -> (a -> a)
$compose = $foldr($o)($id);
