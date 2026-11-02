<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{count, pivot_longer, pivot_wider};
use function Phplyr\starts_with;
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
$wide = $penguins |>
    count('species', 'year') |>
    pivot_wider(
        namesFrom: 'year',
        valuesFrom: 'n',
        namesPrefix: 'n_',
    );
echo $wide, "\n\n";
echo $wide |> pivot_longer(
    starts_with('n_'),
    namesTo: 'year',
    namesPrefix: 'n_',
    valuesTo: 'penguins',
);
