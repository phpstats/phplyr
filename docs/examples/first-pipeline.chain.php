<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)
    ->filter(fn ($r) => $r->body_mass_g !== null)
    ->groupBy('species')
    ->summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    )
    ->arrange(desc('avg_mass_g'));
