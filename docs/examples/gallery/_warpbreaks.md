::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Mean breaks by wool and tension, as a cross-tabulation,
// then a Poisson regression.
$looms = DataFrame::fromCsv("$data/warpbreaks.csv")
    ->asFactor('tension', ['L', 'M', 'H']);

echo $looms |>
    group_by('wool', 'tension') |>
    summarize(
        mean_breaks: mean('breaks'),
        _groups: 'drop',
    ) |>
    mutate(
        mean_breaks: fn ($r) => round($r->mean_breaks, 1),
    ) |>
    pivot_wider(
        namesFrom: 'tension',
        valuesFrom: 'mean_breaks',
    );

$fit = $looms |> glm(
    'breaks ~ wool + tension',
    family: 'poisson',
);
echo "\n\nRate ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-8s %.3f\n", $term, $ratio);
}
```

## PHP 8.3–8.4

```php
// Mean breaks by wool and tension, as a cross-tabulation,
// then a Poisson regression.
$looms = DataFrame::fromCsv("$data/warpbreaks.csv")
    ->asFactor('tension', ['L', 'M', 'H']);

echo phplyr($looms)
    ->groupBy('wool', 'tension')
    ->summarize(
        mean_breaks: mean('breaks'),
        _groups: 'drop',
    )
    ->mutate(
        mean_breaks: fn ($r) => round($r->mean_breaks, 1),
    )
    ->pivotWider(
        namesFrom: 'tension',
        valuesFrom: 'mean_breaks',
    );

$fit = phplyr($looms)->glm(
    'breaks ~ wool + tension',
    family: 'poisson',
);
echo "\n\nRate ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-8s %.3f\n", $term, $ratio);
}
```

:::

```{.text .output}
# A tibble: 2 × 4
  wool      L     M     H
  <chr> <dbl> <dbl> <dbl>
1 A      44.6  24    24.6
2 B      28.2  28.8  18.8

Rate ratios
woolB    0.814
tensionM 0.725
tensionH 0.595
```
