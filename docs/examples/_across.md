::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na() |>
    group_by('species');

// Phplyr helpers can be passed by name; each gets the column name.
echo $penguins |>
    summarize(across(ends_with('_mm'), ['avg' => mean(...), 'sd' => sd(...)]));

// Other closures get the column's values (summarize) or each value (mutate).
echo "\n", $penguins |>
    summarize(across(['flipper_length_mm', 'body_mass_g'], fn (array $v) => \max($v) - \min($v), names: 'range_{col}'));
echo "\n", $penguins |> ungroup() |> mutate(across(ends_with('_mm'), fn ($v) => round($v))) |> head(3);
```

## PHP 8.3–8.4

```php
$penguins = phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->dropNa()
    ->groupBy('species')
    ->get();

// Phplyr helpers can be passed by name; each gets the column name.
echo phplyr($penguins)
    ->summarize(across(ends_with('_mm'), ['avg' => mean(...), 'sd' => sd(...)]));

// Other closures get the column's values (summarize) or each value (mutate).
echo "\n", phplyr($penguins)
    ->summarize(across(['flipper_length_mm', 'body_mass_g'], fn (array $v) => \max($v) - \min($v), names: 'range_{col}'));
echo "\n", phplyr($penguins)->ungroup()->mutate(across(ends_with('_mm'), fn ($v) => round($v)))->head(3);
```

:::

```{.text .output}
# A tibble: 3 × 7
  species   bill_length_mm_avg bill_length_mm_sd bill_depth_mm_avg
  <chr>                  <dbl>             <dbl>             <dbl>
1 Adelie                  38.8              2.66              18.3
2 Chinstrap               48.8              3.34              18.4
3 Gentoo                  47.6              3.11              15.0
# ℹ 3 more variables: bill_depth_mm_sd <dbl>, flipper_length_mm_avg <dbl>,
#   flipper_length_mm_sd <dbl>
# A tibble: 3 × 3
  species   range_flipper_length_mm range_body_mass_g
  <chr>                       <int>             <int>
1 Adelie                         38              1925
2 Chinstrap                      34              2100
3 Gentoo                         28              2350
# A tibble: 3 × 9
  rownames species island    bill_length_mm bill_depth_mm flipper_length_mm
     <int> <chr>   <chr>              <dbl>         <dbl>             <dbl>
1        1 Adelie  Torgersen             39            19               181
2        2 Adelie  Torgersen             40            17               186
3        3 Adelie  Torgersen             40            18               195
# ℹ 3 more variables: body_mass_g <int>, sex <chr>, year <int>
```
