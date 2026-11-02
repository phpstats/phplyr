<?php

use function Phibble\tribble;

$species = DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    summarize(avg_mass_g: mean('body_mass_g', naRm: true));

// A lookup table with one species that isn't in the data.
$scientific = tribble(
    ['species', 'scientific_name'],
    'Adelie', 'Pygoscelis adeliae',
    'Gentoo', 'Pygoscelis papua',
    'Emperor', 'Aptenodytes forsteri',
);

echo $species |> inner_join($scientific, by: 'species');
echo "\n", $species |> left_join($scientific, by: 'species');
echo "\n", $scientific |> anti_join($species, by: 'species');
