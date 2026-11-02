<?php

use function Phibble\tribble;

$species = phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->groupBy('species')
    ->summarize(avg_mass_g: mean('body_mass_g', naRm: true))
    ->get();

// A lookup table with one species that isn't in the data.
$scientific = tribble(
    ['species', 'scientific_name'],
    'Adelie', 'Pygoscelis adeliae',
    'Gentoo', 'Pygoscelis papua',
    'Emperor', 'Aptenodytes forsteri',
);

echo phplyr($species)->innerJoin($scientific, by: 'species');
echo "\n", phplyr($species)->leftJoin($scientific, by: 'species');
echo "\n", phplyr($scientific)->antiJoin($species, by: 'species');
