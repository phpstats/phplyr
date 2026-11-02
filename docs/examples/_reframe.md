::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
echo DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na('body_mass_g') |>
    group_by('species') |>
    reframe(
        p: [0.25, 0.5, 0.75],
        body_mass_g: fn ($g) => array_map(fn ($p) => Stats::quantile($g->column('body_mass_g'), $p), [0.25, 0.5, 0.75]),
    );
```

## PHP 8.3–8.4

```php
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->dropNa('body_mass_g')
    ->groupBy('species')
    ->reframe(
        p: [0.25, 0.5, 0.75],
        body_mass_g: fn ($g) => array_map(fn ($p) => Stats::quantile($g->column('body_mass_g'), $p), [0.25, 0.5, 0.75]),
    );
```

:::

```{.text .output}
# A tibble: 9 × 3
  species       p body_mass_g
  <chr>     <dbl>       <dbl>
1 Adelie     0.25       3350 
2 Adelie     0.5        3700 
3 Adelie     0.75       4000 
4 Chinstrap  0.25       3488.
5 Chinstrap  0.5        3700 
6 Chinstrap  0.75       3950 
7 Gentoo     0.25       4700 
8 Gentoo     0.5        5000 
9 Gentoo     0.75       5500
```
