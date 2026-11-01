<?php

// Students nested in schools: shared fixed effects and
// one intercept shift per school.
$schools = DataFrame::fromCsv('schools.csv');
$formula = 'score ~ study_hours + prior_score'
    . ' + (1 | school)';
$fit = phplyr($schools)->lmer($formula);

foreach ($fit['columns'] as $i => $term) {
    printf(
        "%-12s %8.4f  (se %.4f)\n",
        $term,
        $fit['coefficients'][$i],
        $fit['std_errors'][$i],
    );
}
$schoolSd = $fit['theta'][0] * $fit['sigma'];
$icc = $schoolSd ** 2
    / ($schoolSd ** 2 + $fit['sigma'] ** 2);
printf(
    "school sd %.3f, residual sd %.3f, ICC %.3f\n\n",
    $schoolSd,
    $fit['sigma'],
    $icc,
);

// Each school's raw mean score beside its estimated
// intercept shift.
$random = $fit['random'][0];
$shifts = array_combine(
    $random['group_labels'],
    array_column($random['effects'], 0),
);
echo phplyr($schools)
    ->groupBy('school')
    ->summarize(students: n(), raw_mean: mean('score'))
    ->mutate(
        intercept_shift: fn ($r) =>
            round($shifts[$r->school], 2),
        raw_mean: fn ($r) => round($r->raw_mean, 1),
    )
    ->arrange(desc('intercept_shift'))
    ->head(5);
