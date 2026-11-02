<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{n, mean, max};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo $penguins |>
    group_by('species') |>
    summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g', naRm: true),
        max_mass_g: max('body_mass_g', naRm: true),
    ) |>
    arrange(desc('avg_mass_g'));
