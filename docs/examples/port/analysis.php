<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, mutate, select, count, head};
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{pivot_longer, pivot_wider};
use function Phplyr\{case_when, if_else, col, row_number};
use function Phplyr\{n, mean, sd, lm, glm, lmer};

$schools = DataFrame::fromCsv('schools.csv');

// Clean: drop incomplete rows, band study time,
// compute the gain.
$hours = col('study_hours');
$clean = $schools |>
    filter(col('score')->isNa()->not(), $hours->gt(0)) |>
    mutate(
        hours_band: case_when(
            [$hours->lt(4), 'low'],
            [$hours->lt(8), 'mid'],
            default: 'high',
        ),
        gain: col('score')->sub(col('prior_score')),
        passed: if_else(col('score')->ge(75), 1, 0),
    );

// Reshape: one row per student and test.
$long = $clean |>
    mutate(student: row_number()) |>
    select(
        'student',
        'school',
        ['prior' => 'prior_score', 'final' => 'score'],
    ) |>
    pivot_longer(
        ['prior', 'final'],
        namesTo: 'test',
        valuesTo: 'points',
    );

echo $long |>
    group_by('test') |>
    summarize(
        mean: mean('points'),
        sd: sd('points'),
    ), "\n";

// Summarize: students per school and band, then
// the schools with the largest mean gains.
echo $clean |>
    count('school', 'hours_band') |>
    pivot_wider(
        namesFrom: 'hours_band',
        valuesFrom: 'n',
        valuesFill: 0,
    ) |>
    head(3), "\n";

echo $clean |>
    group_by('school') |>
    summarize(n: n(), mean_gain: mean('gain')) |>
    arrange(desc('mean_gain')) |>
    head(3), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
$rhs = 'study_hours + prior_score';
echo $coef($clean |> lm("score ~ {$rhs}")), "\n";
echo $coef($clean |> glm(
    "passed ~ {$rhs}",
    family: 'binomial',
)), "\n";
echo $coef($clean |> lmer(
    "score ~ {$rhs} + (1 | school)",
)), "\n";
