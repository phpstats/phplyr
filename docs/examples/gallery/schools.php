<?php

// Fit one regression on all students, then average
// each school's residuals.
$schools = DataFrame::fromCsv('schools.csv');
$fit = $schools |>
    lm('score ~ prior_score + study_hours');
[$b0, $bPrior, $bHours] = $fit['coefficients'];

$expected = fn ($r) => $b0
    + $bPrior * $r->prior_score
    + $bHours * $r->study_hours;
$shareAbove = fn ($g) => round(\count(array_filter(
    $g->column('residual'),
    fn ($e) => $e > 0,
)) / $g->nrow(), 2);

echo $schools |>
    mutate(
        residual: fn ($r) => $r->score - $expected($r),
    ) |>
    group_by('school') |>
    summarize(
        students: n(),
        avg_score: mean('score'),
        avg_residual: mean('residual'),
        share_above: $shareAbove,
    ) |>
    mutate(
        rank: min_rank(desc('avg_residual')),
        avg_score: fn ($r) => round($r->avg_score, 1),
        avg_residual: fn ($r) => round($r->avg_residual, 2),
    ) |>
    arrange('rank');
