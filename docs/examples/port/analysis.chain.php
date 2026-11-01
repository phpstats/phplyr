<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, case_when, if_else};
use function Phplyr\{col, row_number, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

// Clean: drop incomplete rows, band study time,
// compute the gain.
$hours = col('study_hours');
$clean = phplyr($schools)
    ->filter(col('score')->isNa()->not(), $hours->gt(0))
    ->mutate(
        hours_band: case_when(
            [$hours->lt(4), 'low'],
            [$hours->lt(8), 'mid'],
            default: 'high',
        ),
        gain: col('score')->sub(col('prior_score')),
        passed: if_else(col('score')->ge(75), 1, 0),
    )
    ->get();

// Reshape: one row per student and test.
$long = phplyr($clean)
    ->mutate(student: row_number())
    ->select(
        'student',
        'school',
        ['prior' => 'prior_score', 'final' => 'score'],
    )
    ->pivotLonger(
        ['prior', 'final'],
        namesTo: 'test',
        valuesTo: 'points',
    )
    ->get();

echo phplyr($long)
    ->groupBy('test')
    ->summarize(
        mean: mean('points'),
        sd: sd('points'),
    ), "\n";

// Summarize: students per school and band, then
// the schools with the largest mean gains.
echo phplyr($clean)
    ->count('school', 'hours_band')
    ->pivotWider(
        namesFrom: 'hours_band',
        valuesFrom: 'n',
        valuesFill: 0,
    )
    ->head(3), "\n";

echo phplyr($clean)
    ->groupBy('school')
    ->summarize(n: n(), mean_gain: mean('gain'))
    ->arrange(desc('mean_gain'))
    ->head(3), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
$rhs = 'study_hours + prior_score';
echo $coef(phplyr($clean)->lm("score ~ {$rhs}")), "\n";
echo $coef(phplyr($clean)->glm(
    "passed ~ {$rhs}",
    family: 'binomial',
)), "\n";
echo $coef(phplyr($clean)->lmer(
    "score ~ {$rhs} + (1 | school)",
)), "\n";
