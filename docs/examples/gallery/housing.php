<?php

// Rent per square meter by number of bedrooms, and each
// flat's difference from its bedroom group's mean.
$housing = DataFrame::fromCsv('housing.csv') |>
    mutate(per_m2: fn ($r) => round(
        $r->monthly_rent / $r->area_m2,
        2,
    ));

echo $housing |>
    group_by('bedrooms') |>
    summarize(
        across(
            ['area_m2', 'monthly_rent', 'per_m2'],
            ['mean' => mean(...), 'max' => max(...)],
        ),
        flats: n(),
    ) |>
    mutate(across(
        where('double'),
        fn ($v) => round($v, 1),
    ));

$vsGroup = fn ($r, $g) => round(
    $r->per_m2 - mean('per_m2')($g),
    2,
);
echo "\n", $housing |>
    mutate(vs_group: $vsGroup, _by: 'bedrooms') |>
    arrange('bedrooms', desc('vs_group'));
