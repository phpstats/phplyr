<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, max};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo phplyr($penguins)
    ->groupBy('species')
    ->summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g', naRm: true),
        max_mass_g: max('body_mass_g', naRm: true),
    )
    ->arrange(desc('avg_mass_g'));
