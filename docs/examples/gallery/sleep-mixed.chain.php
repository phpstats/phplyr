<?php

// Days nested in subjects: a shared intercept and slope,
// and one intercept shift per subject.
$sleep = DataFrame::fromCsv("$data/sleepstudy.csv");
$formula = 'Reaction ~ Days + (1 | Subject)';
$fit = phplyr($sleep)->lmer($formula);

foreach ($fit['columns'] as $i => $term) {
    printf(
        "%-12s %8.4f  (se %.4f)\n",
        $term,
        $fit['coefficients'][$i],
        $fit['std_errors'][$i],
    );
}
$subjectSd = $fit['theta'][0] * $fit['sigma'];
$icc = $subjectSd ** 2
    / ($subjectSd ** 2 + $fit['sigma'] ** 2);
printf(
    "subject sd %.3f, residual sd %.3f, ICC %.3f\n\n",
    $subjectSd,
    $fit['sigma'],
    $icc,
);

// Each subject's raw mean reaction time beside its
// estimated intercept shift.
$random = $fit['random'][0];
$shifts = array_combine(
    $random['group_labels'],
    array_column($random['effects'], 0),
);
echo phplyr($sleep)
    ->groupBy('Subject')
    ->summarize(days: n(), raw_mean: mean('Reaction'))
    ->mutate(
        intercept_shift: fn ($r) =>
            round($shifts[$r->Subject], 1),
        raw_mean: fn ($r) => round($r->raw_mean, 1),
    )
    ->arrange(desc('intercept_shift'))
    ->head(5);
