<?php

$scores = DataFrame::fromRows([
    ['student' => 'ann', 'wk1' => 10, 'wk2' => null, 'wk3' => 30],
    ['student' => 'bob', 'wk1' => 7, 'wk2' => 8, 'wk3' => 9],
]);

$long = phplyr($scores)->pivotLonger(starts_with('wk'), namesTo: 'week', namesPrefix: 'wk', valuesTo: 'points', valuesDropNa: true)->get();
echo $long;

echo "\n", phplyr($long)->pivotWider(namesFrom: 'week', valuesFrom: 'points', namesPrefix: 'week_', valuesFill: 0, namesSort: true);
