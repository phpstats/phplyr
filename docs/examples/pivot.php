<?php

$scores = DataFrame::fromRows([
    ['student' => 'ann', 'wk1' => 10, 'wk2' => null, 'wk3' => 30],
    ['student' => 'bob', 'wk1' => 7, 'wk2' => 8, 'wk3' => 9],
]);

$long = $scores |> pivot_longer(starts_with('wk'), namesTo: 'week', namesPrefix: 'wk', valuesTo: 'points', valuesDropNa: true);
echo $long;

echo "\n", $long |> pivot_wider(namesFrom: 'week', valuesFrom: 'points', namesPrefix: 'week_', valuesFill: 0, namesSort: true);
