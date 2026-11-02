<?php

// Fit one line for all subjects, then average each
// subject's residuals.
$sleep = DataFrame::fromCsv("$data/sleepstudy.csv");
$fit = phplyr($sleep)->lm('Reaction ~ Days');
[$b0, $bDays] = $fit['coefficients'];

$expected = fn ($r) => $b0 + $bDays * $r->Days;
$shareAbove = fn ($g) => round(\count(array_filter(
    $g->column('residual'),
    fn ($e) => $e > 0,
)) / $g->nrow(), 2);

echo phplyr($sleep)
    ->mutate(
        residual: fn ($r) => $r->Reaction - $expected($r),
    )
    ->groupBy('Subject')
    ->summarize(
        days: n(),
        avg_reaction: mean('Reaction'),
        avg_residual: mean('residual'),
        share_above: $shareAbove,
    )
    ->mutate(
        rank: min_rank(desc('avg_residual')),
        avg_reaction: fn ($r) => round($r->avg_reaction, 1),
        avg_residual: fn ($r) => round($r->avg_residual, 1),
    )
    ->arrange('rank')
    ->head(5);
