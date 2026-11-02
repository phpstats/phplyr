::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
echo DataFrame::fromCsv("$data/penguins.csv") |>
    summarize(
        penguins: n(),
        species: n_distinct('species'),
        median_mass_g: median('body_mass_g', naRm: true),
        q90_flipper_mm: quantile('flipper_length_mm', 0.9, naRm: true),
        iqr_bill_mm: iqr('bill_length_mm', naRm: true),
        heaviest: fn ($g) => ($g |> slice_max('body_mass_g', n: 1) |> pull('species'))[0],
    );
```

## PHP 8.3–8.4

```php
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->summarize(
        penguins: n(),
        species: n_distinct('species'),
        median_mass_g: median('body_mass_g', naRm: true),
        q90_flipper_mm: quantile('flipper_length_mm', 0.9, naRm: true),
        iqr_bill_mm: iqr('bill_length_mm', naRm: true),
        heaviest: fn ($g) => phplyr($g)->sliceMax('body_mass_g', n: 1)->pull('species')[0],
    );
```

:::

```{.text .output}
# A tibble: 1 × 6
  penguins species median_mass_g q90_flipper_mm iqr_bill_mm heaviest
     <int>   <int>         <dbl>          <dbl>       <dbl> <chr>   
1      344       3          4050           221.        9.27 Gentoo
```
