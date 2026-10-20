<?php

$housing = DataFrame::fromCsv('housing.csv');

// keep: 'none' keeps only new columns (and grouping columns).
echo $housing |> mutate(rent_k: fn ($r) => $r->monthly_rent / 1000, keep: 'none') |> head(3);

// by groups for this call only; mean() is recycled within each group.
echo "\n", $housing
    |> mutate(vs_bedroom_avg: fn ($r, $g) => $r->monthly_rent - mean('monthly_rent')($g), by: 'bedrooms', after: 'bedrooms')
    |> head(4);
