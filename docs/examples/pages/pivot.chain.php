<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{phplyr, starts_with};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
$wide = phplyr($penguins)
    ->count('species', 'year')
    ->pivotWider(
        namesFrom: 'year',
        valuesFrom: 'n',
        namesPrefix: 'n_',
    )
    ->get();
echo $wide, "\n\n";
echo phplyr($wide)->pivotLonger(
    starts_with('n_'),
    namesTo: 'year',
    namesPrefix: 'n_',
    valuesTo: 'penguins',
);
