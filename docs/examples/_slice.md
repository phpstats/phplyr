::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// The two heaviest penguins of each species.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    slice_max('body_mass_g', n: 2) |>
    select('species', 'island', 'sex', 'body_mass_g');
```

## PHP 8.3–8.4

```php
// The two heaviest penguins of each species.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->groupBy('species')
    ->sliceMax('body_mass_g', n: 2)
    ->select('species', 'island', 'sex', 'body_mass_g');
```

:::

```{.text .output}
# A tibble: 6 × 4
# Groups:   species [3]
  species   island sex   body_mass_g
  <chr>     <chr>  <chr>       <int>
1 Adelie    Biscoe male         4775
2 Adelie    Biscoe male         4725
3 Chinstrap Dream  male         4800
4 Chinstrap Dream  male         4550
5 Gentoo    Biscoe male         6300
6 Gentoo    Biscoe male         6050
```
