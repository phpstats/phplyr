<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

// _keep: 'none' keeps only new columns (and grouping columns).
echo $penguins |> mutate(mass_kg: col('body_mass_g')->div(1000), _keep: 'none') |> head(3);

// _by groups for this call only; mean() is recycled within each group.
echo "\n", $penguins |>
    select('species', 'island', 'body_mass_g') |>
    mutate(vs_species_avg: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), _by: 'species', _after: 'body_mass_g') |>
    head(4);
