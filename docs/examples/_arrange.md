::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Heaviest first within each species.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    select('species', 'island', 'sex', 'body_mass_g') |>
    arrange('species', desc('body_mass_g'));
```

## PHP 8.3–8.4

```php
// Heaviest first within each species.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->select('species', 'island', 'sex', 'body_mass_g')
    ->arrange('species', desc('body_mass_g'));
```

:::

```{.text .output}
# A tibble: 344 × 4
   species island    sex   body_mass_g
   <chr>   <chr>     <chr>       <int>
 1 Adelie  Biscoe    male         4775
 2 Adelie  Biscoe    male         4725
 3 Adelie  Torgersen male         4700
 4 Adelie  Torgersen male         4675
 5 Adelie  Dream     male         4650
 6 Adelie  Dream     male         4600
 7 Adelie  Biscoe    male         4600
 8 Adelie  Torgersen male         4500
 9 Adelie  Dream     male         4475
10 Adelie  Torgersen male         4450
# ℹ 334 more rows
```
