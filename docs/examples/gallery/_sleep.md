::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Fit one line for all subjects, then average each
// subject's residuals.
$sleep = DataFrame::fromCsv("$data/sleepstudy.csv");
$fit = $sleep |> lm('Reaction ~ Days');
[$b0, $bDays] = $fit['coefficients'];

$expected = fn ($r) => $b0 + $bDays * $r->Days;
$shareAbove = fn ($g) => round(\count(array_filter(
    $g->column('residual'),
    fn ($e) => $e > 0,
)) / $g->nrow(), 2);

echo $sleep |>
    mutate(
        residual: fn ($r) => $r->Reaction - $expected($r),
    ) |>
    group_by('Subject') |>
    summarize(
        days: n(),
        avg_reaction: mean('Reaction'),
        avg_residual: mean('residual'),
        share_above: $shareAbove,
    ) |>
    mutate(
        rank: min_rank(desc('avg_residual')),
        avg_reaction: fn ($r) => round($r->avg_reaction, 1),
        avg_residual: fn ($r) => round($r->avg_residual, 1),
    ) |>
    arrange('rank') |>
    head(5);
```

## PHP 8.3–8.4

```php
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
```

:::

```{.text .output}
# A tibble: 5 × 6
  Subject  days avg_reaction avg_residual share_above  rank
    <int> <int>        <dbl>        <dbl>       <dbl> <int>
1     337    10         376.         77.2         1       1
2     308    10         342.         43.6         0.6     2
3     352    10         337.         38.9         0.9     3
4     372    10         318.         19.4         0.9     4
5     333    10         316.         17.7         0.9     5
```
