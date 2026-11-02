<?php

// Fuel economy, power, and weight by number of cylinders,
// and each car's mpg against its cylinder group's mean.
$cars = phplyr(DataFrame::fromCsv("$data/mtcars.csv"))
    ->rename(model: 'rownames')
    ->get();

echo phplyr($cars)
    ->groupBy('cyl')
    ->summarize(
        across(
            ['mpg', 'hp', 'wt'],
            ['mean' => mean(...), 'max' => max(...)],
        ),
        cars: n(),
    )
    ->mutate(across(
        where('double'),
        fn ($v) => round($v, 1),
    ));

$vsGroup = fn ($r, $g) => round(
    $r->mpg - mean('mpg')($g),
    1,
);
echo "\n\n", phplyr($cars)
    ->select('model', 'cyl', 'mpg')
    ->mutate(vs_group: $vsGroup, _by: 'cyl')
    ->arrange('cyl', desc('vs_group'));
