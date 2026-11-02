::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Labor-force participation by age quartile, then a
// logistic regression.
$mroz = DataFrame::fromCsv("$data/mroz.csv") |>
    mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0));

echo $mroz |>
    mutate(age_quartile: ntile('age', 4)) |>
    group_by('age_quartile') |>
    summarize(
        women: n(),
        min_age: min('age'),
        max_age: max('age'),
        participation: mean('in_lf'),
    );

$fit = $mroz |> glm(
    'in_lf ~ age + k5 + k618 + inc',
    family: 'binomial',
);
echo "\n\nOdds ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-5s %.3f\n", $term, $ratio);
}
```

## PHP 8.3–8.4

```php
// Labor-force participation by age quartile, then a
// logistic regression.
$mroz = phplyr(DataFrame::fromCsv("$data/mroz.csv"))
    ->mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0))
    ->get();

echo phplyr($mroz)
    ->mutate(age_quartile: ntile('age', 4))
    ->groupBy('age_quartile')
    ->summarize(
        women: n(),
        min_age: min('age'),
        max_age: max('age'),
        participation: mean('in_lf'),
    );

$fit = phplyr($mroz)->glm(
    'in_lf ~ age + k5 + k618 + inc',
    family: 'binomial',
);
echo "\n\nOdds ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-5s %.3f\n", $term, $ratio);
}
```

:::

```{.text .output}
# A tibble: 4 × 5
  age_quartile women min_age max_age participation
         <int> <int>   <int>   <int>         <dbl>
1            1   189      30      36         0.603
2            2   188      36      43         0.606
3            3   188      43      49         0.601
4            4   188      49      60         0.463

Odds ratios
age   0.936
k5    0.257
k618  0.890
inc   0.982
```
