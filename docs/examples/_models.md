::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$fit = DataFrame::fromCsv("$data/mtcars.csv") |>
    lm('mpg ~ wt + hp');

foreach ($fit['columns'] as $i => $term) {
    printf("%-12s %8.4f  (se %.4f)\n", $term, $fit['coefficients'][$i], $fit['std_errors'][$i]);
}
printf("R² = %.4f, n = %d\n", $fit['r_squared'], $fit['n_observations']);

$logit = DataFrame::fromCsv("$data/mroz.csv") |>
    mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0)) |>
    glm('in_lf ~ age + k5 + inc', family: 'binomial');
echo 'logit coefficients: ', implode(', ', array_map(fn ($b) => round($b, 4), $logit['coefficients'])), "\n";
```

## PHP 8.3–8.4

```php
$fit = phplyr(DataFrame::fromCsv("$data/mtcars.csv"))
    ->lm('mpg ~ wt + hp');

foreach ($fit['columns'] as $i => $term) {
    printf("%-12s %8.4f  (se %.4f)\n", $term, $fit['coefficients'][$i], $fit['std_errors'][$i]);
}
printf("R² = %.4f, n = %d\n", $fit['r_squared'], $fit['n_observations']);

$logit = phplyr(DataFrame::fromCsv("$data/mroz.csv"))
    ->mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0))
    ->glm('in_lf ~ age + k5 + inc', family: 'binomial');
echo 'logit coefficients: ', implode(', ', array_map(fn ($b) => round($b, 4), $logit['coefficients'])), "\n";
```

:::

```{.text .output}
(Intercept)   37.2273  (se 1.5988)
wt            -3.8778  (se 0.6327)
hp            -0.0318  (se 0.0090)
R² = 0.8268, n = 32
logit coefficients: 3.3944, -0.0569, -1.3133, -0.0188
```
