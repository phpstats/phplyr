::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Days nested in subjects: a shared intercept and slope,
// and one intercept shift per subject.
$sleep = DataFrame::fromCsv("$data/sleepstudy.csv");
$formula = 'Reaction ~ Days + (1 | Subject)';
$fit = $sleep |> lmer($formula);

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
echo $sleep |>
    group_by('Subject') |>
    summarize(days: n(), raw_mean: mean('Reaction')) |>
    mutate(
        intercept_shift: fn ($r) =>
            round($shifts[$r->Subject], 1),
        raw_mean: fn ($r) => round($r->raw_mean, 1),
    ) |>
    arrange(desc('intercept_shift')) |>
    head(5);
```

## PHP 8.3–8.4

```php
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
```

:::

```{.text .output}
(Intercept)  251.4051  (se 9.7467)
Days          10.4673  (se 0.8042)
subject sd 37.124, residual sd 30.991, ICC 0.589

# A tibble: 5 × 4
  Subject  days raw_mean intercept_shift
    <int> <int>    <dbl>           <dbl>
1     337    10     376.            72.2
2     308    10     342.            40.8
3     352    10     337.            36.4
4     372    10     318.            18.1
5     333    10     316.            16.5
```
