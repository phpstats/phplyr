<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, group_by, summarize};
use function Phplyr\{arrange, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |>
    filter(fn ($r) => $r->body_mass_g !== null) |>
    group_by('species') |>
    summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    ) |>
    arrange(desc('avg_mass_g'));
