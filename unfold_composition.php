<?php

////////////////////////////////////////////////////////////////
// compose = foldl (.) id
////////////////////////////////////////////////////////////////

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

// foldl :: (a -> b -> a) -> a -> [b] -> a
$foldl = curry(function (callable $f, $acc, $head, ...$tail) {
    return array_reduce(count($tail) > 0
        ? array_merge([$head], $tail)
        : $head,
    $f, $acc);
});

// compose :: [a -> a] -> (a -> a)
$compose = $foldl($o, $id);

////////////////////////////////////////////////////////////////
// An example of how compose() unfolds
////////////////////////////////////////////////////////////////

// 3 simple functions that prepend their name to a string
$f = function ($s) { return 'f' . $s; };
$g = function ($s) { return 'g' . $s; };
$h = function ($s) { return 'h' . $s; };

// Data travels through the composed functions from right to left
assert( $h($g($f(''))) === $compose($h, $g, $f)('') ); // 'hgf' === 'hgf'

// Some simple algebraic substitutions
assert( $compose($h, $g, $f)('')          === $foldl($o, $id)($h, $g, $f)('')   );
assert( $foldl($o, $id)($h, $g, $f)('')   === $foldl($o, $id, [$h, $g, $f])('') );
assert( $foldl($o, $id, [$h, $g, $f])('') === $o($o($o($id, $h), $g), $f)('')   );

// Expand step 1: $id_h = $o($id, $h)
$id_h = function ($x) {
    $id = function ($x) { return $x; };
    $h = function ($s) { return 'h'.$s; };
    return $id($h($x));
};
assert( $id_h('') === $o($id, $h)('') );

// Expand step 2: $id_h_g = $o($id_h, $g) = $o($o($id, $h), $g)
$id_h_g = function ($x) {
    $id_h = function ($x) {
        $id = function ($x) { return $x; };
        $h = function ($s) { return 'h'.$s; };
        return $id($h($x));
    };
    $g = function ($s) { return 'g'.$s; };
    return $id_h($g($x));
};
assert( $id_h_g('') === $o($id_h, $g)('') );

// Expand step 3: $id_h_g_f = $o($id_h_g, $f) = $o($o($o($id, $h), $g), $f)
$id_h_g_f = function ($x) {
    $id_h_g = function ($x) {
        $id_h = function ($x) {
            $id = function ($x) { return $x; };
            $h = function ($s) { return 'h'.$s; };
            return $id($h($x));
        };
        $g = function ($s) { return 'g'.$s; };
        return $id_h($g($x));
    };
    $f = function ($s) { return 'f'.$s; };
    return $id_h_g($f($x));
};
assert($o($id_h_g, $f)('') === $id_h_g_f(''));

// Prove that $id_h_g_f is our original composition
assert( $id_h_g_f('') === $compose($h, $g, $f)('') );
