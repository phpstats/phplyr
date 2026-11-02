::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// _keep: 'none' keeps only new columns (and grouping columns).
echo $penguins |> mutate(mass_kg: col('body_mass_g')->div(1000), _keep: 'none') |> head(3);

// _by groups for this call only; mean() is recycled within each group.
echo "\n", $penguins |>
    select('species', 'island', 'body_mass_g') |>
    mutate(vs_species_avg: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), _by: 'species', _after: 'body_mass_g') |>
    head(4);
```

## PHP 8.3–8.4

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// _keep: 'none' keeps only new columns (and grouping columns).
echo phplyr($penguins)->mutate(mass_kg: col('body_mass_g')->div(1000), _keep: 'none')->head(3);

// _by groups for this call only; mean() is recycled within each group.
echo "\n", phplyr($penguins)
    ->select('species', 'island', 'body_mass_g')
    ->mutate(vs_species_avg: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), _by: 'species', _after: 'body_mass_g')
    ->head(4);
```

:::

```{.text .output}
# A tibble: 3 × 1
  mass_kg
    <dbl>
1    3.75
2    3.8 
3    3.25
# A tibble: 4 × 4
  species island    body_mass_g vs_species_avg
  <chr>   <chr>           <int>          <dbl>
1 Adelie  Torgersen        3750           49.3
2 Adelie  Torgersen        3800           99.3
3 Adelie  Torgersen        3250         -451. 
4 Adelie  Torgersen          NA           NA
```
