<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, mutate, select, count};
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{pivot_longer, pivot_wider};
use function Phplyr\{case_when, if_else, col, row_number};
use function Phplyr\{n, mean, sd, lm, glm, lmer};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Clean: drop incomplete rows, band body mass,
// compute the bill ratio.
$mass = col('body_mass_g');
$clean = $penguins |>
    filter(
        $mass->isNa()->not(),
        col('sex')->isNa()->not(),
    ) |>
    mutate(
        mass_band: case_when(
            [$mass->lt(3500), 'light'],
            [$mass->lt(4500), 'mid'],
            default: 'heavy',
        ),
        bill_ratio: col('bill_length_mm')
            ->div(col('bill_depth_mm')),
        male: if_else(col('sex')->eq('male'), 1, 0),
    );

// Reshape: one row per penguin and bill measure.
$long = $clean |>
    mutate(penguin: row_number()) |>
    select(
        'penguin',
        'species',
        ['length' => 'bill_length_mm'],
        ['depth' => 'bill_depth_mm'],
    ) |>
    pivot_longer(
        ['length', 'depth'],
        namesTo: 'measure',
        valuesTo: 'mm',
    );

echo $long |>
    group_by('measure') |>
    summarize(mean: mean('mm'), sd: sd('mm')), "\n";

// Summarize: penguins per species and mass band, then
// the species by mean bill ratio.
echo $clean |>
    count('species', 'mass_band') |>
    pivot_wider(
        namesFrom: 'mass_band',
        valuesFrom: 'n',
        valuesFill: 0,
    ), "\n";

echo $clean |>
    group_by('species') |>
    summarize(n: n(), mean_ratio: mean('bill_ratio')) |>
    arrange(desc('mean_ratio')), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
echo $coef($clean |> lm(
    'body_mass_g ~ flipper_length_mm + male',
)), "\n";
echo $coef($clean |> glm(
    'male ~ body_mass_g + bill_depth_mm',
    family: 'binomial',
)), "\n";
echo $coef($clean |> lmer(
    'body_mass_g ~ flipper_length_mm + male'
    . ' + (1 | species)',
)), "\n";
