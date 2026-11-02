<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{phplyr, if_else, col};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo phplyr($penguins)
    ->filter(fn ($r) => $r->body_mass_g !== null)
    ->select('species', 'island', 'sex', 'body_mass_g')
    ->mutate(
        mass_kg: fn ($r) => $r->body_mass_g / 1000,
        size: if_else(
            col('body_mass_g')->ge(4500),
            'large',
            'small',
        ),
    );
