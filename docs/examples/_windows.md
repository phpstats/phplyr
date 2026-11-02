::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Female Chinstrap penguins, from the shortest flippers up.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    filter(fn ($r) => $r->species === 'Chinstrap' && $r->sex === 'female') |>
    arrange('flipper_length_mm') |>
    mutate(
        prev: lag('body_mass_g'),
        change: lag('body_mass_g')->map(fn ($prev, $r) => $prev === null ? null : $r->body_mass_g - $prev),
        running_mean: cummean('body_mass_g'),
        rank: min_rank(desc('body_mass_g')),
        pct: percent_rank('body_mass_g'),
        quartile: ntile('body_mass_g', 4),
    ) |>
    select('flipper_length_mm', 'body_mass_g', 'prev', 'change', 'running_mean', 'rank', 'pct', 'quartile') |>
    head(8);
```

## PHP 8.3–8.4

```php
// Female Chinstrap penguins, from the shortest flippers up.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->filter(fn ($r) => $r->species === 'Chinstrap' && $r->sex === 'female')
    ->arrange('flipper_length_mm')
    ->mutate(
        prev: lag('body_mass_g'),
        change: lag('body_mass_g')->map(fn ($prev, $r) => $prev === null ? null : $r->body_mass_g - $prev),
        running_mean: cummean('body_mass_g'),
        rank: min_rank(desc('body_mass_g')),
        pct: percent_rank('body_mass_g'),
        quartile: ntile('body_mass_g', 4),
    )
    ->select('flipper_length_mm', 'body_mass_g', 'prev', 'change', 'running_mean', 'rank', 'pct', 'quartile')
    ->head(8);
```

:::

```{.text .output}
# A tibble: 8 × 8
  flipper_length_mm body_mass_g  prev change running_mean  rank    pct quartile
              <int>       <int> <int>  <int>        <dbl> <int>  <dbl>    <int>
1               178        3250    NA     NA        3250     30 0.0909        1
2               181        3700  3250    450        3475      7 0.758         3
3               181        3600  3700   -100        3517.    15 0.545         3
4               185        3700  3600    100        3562.     7 0.758         4
5               187        2900  3700   -800        3430     33 0.0303        1
6               187        3200  2900    300        3392.    32 0.0606        1
7               187        3350  3200    150        3386.    26 0.212         1
8               187        3650  3350    300        3419.    12 0.606         3
```
