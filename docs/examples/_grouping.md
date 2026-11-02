::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv") |> group_by('species');

echo json_encode([
    'groups' => $penguins |> group_vars(),
    'n_groups' => $penguins |> n_groups(),
    'sizes' => $penguins |> group_size(),
]), "\n\n";

// Grouped mutate: helpers are evaluated within each group.
echo $penguins |>
    mutate(centered: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), rank_in_species: min_rank(desc('body_mass_g'))) |>
    ungroup() |>
    select('species', 'body_mass_g', 'centered', 'rank_in_species') |>
    head(4);

// One table per group.
$parts = $penguins |> group_split();
echo "\n", \count($parts), ' tables; the first has ', $parts[0]->nrow(), " rows\n";
```

## PHP 8.3–8.4

```php
$penguins = phplyr(DataFrame::fromCsv("$data/penguins.csv"))->groupBy('species')->get();

echo json_encode([
    'groups' => phplyr($penguins)->groupVars(),
    'n_groups' => phplyr($penguins)->nGroups(),
    'sizes' => phplyr($penguins)->groupSize(),
]), "\n\n";

// Grouped mutate: helpers are evaluated within each group.
echo phplyr($penguins)
    ->mutate(centered: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), rank_in_species: min_rank(desc('body_mass_g')))
    ->ungroup()
    ->select('species', 'body_mass_g', 'centered', 'rank_in_species')
    ->head(4);

// One table per group.
$parts = phplyr($penguins)->groupSplit();
echo "\n", \count($parts), ' tables; the first has ', $parts[0]->nrow(), " rows\n";
```

:::

```{.text .output}
{"groups":["species"],"n_groups":3,"sizes":[152,68,124]}

# A tibble: 4 × 4
  species body_mass_g centered rank_in_species
  <chr>         <int>    <dbl>           <int>
1 Adelie         3750     49.3              65
2 Adelie         3800     99.3              56
3 Adelie         3250   -451.              125
4 Adelie           NA     NA                NA
3 tables; the first has 152 rows
```
