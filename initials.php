<?php
require __DIR__.'/lib.php';

$explode = curry('explode');
$implode = curry('implode');
$match   = curry('preg_match');
$head    = function ($s) { return $s[0]; };
$isWord  = $match('/[a-z]/i');

$initials = $compose([
    $implode(' '),
    $map($o('strtoupper')($head)),
    $filter($isWord),
    $explode(' ')
]);

assert($initials('This is rather cool :)') === 'T I R C');
