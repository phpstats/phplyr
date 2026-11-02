::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
echo DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na() |>
    select('species', 'bill_length_mm', 'bill_depth_mm', 'flipper_length_mm', 'body_mass_g') |>
    mutate(
        bill_ratio: fn ($r) => round($r->bill_length_mm / $r->bill_depth_mm, 2),
        flipper: if_else(col('flipper_length_mm')->ge(200), 'long', 'short'),
        rank: min_rank(desc('body_mass_g')),
    ) |>
    arrange('rank') |>
    head(6);
```

## PHP 8.3–8.4

```php
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->dropNa()
    ->select('species', 'bill_length_mm', 'bill_depth_mm', 'flipper_length_mm', 'body_mass_g')
    ->mutate(
        bill_ratio: fn ($r) => round($r->bill_length_mm / $r->bill_depth_mm, 2),
        flipper: if_else(col('flipper_length_mm')->ge(200), 'long', 'short'),
        rank: min_rank(desc('body_mass_g')),
    )
    ->arrange('rank')
    ->head(6);
```

:::

```{.text .output}
# A tibble: 6 × 8
  species bill_length_mm bill_depth_mm flipper_length_mm body_mass_g bill_ratio
  <chr>            <dbl>         <dbl>             <int>       <int>      <dbl>
1 Gentoo            49.2          15.2               221        6300       3.24
2 Gentoo            59.6          17                 230        6050       3.51
3 Gentoo            51.1          16.3               220        6000       3.13
4 Gentoo            48.8          16.2               222        6000       3.01
5 Gentoo            45.2          16.4               223        5950       2.76
6 Gentoo            49.8          15.9               229        5950       3.13
# ℹ 2 more variables: flipper <chr>, rank <int>
```
