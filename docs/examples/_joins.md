::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use function Phibble\tribble;

$species = DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    summarize(avg_mass_g: mean('body_mass_g', naRm: true));

// A lookup table with one species that isn't in the data.
$scientific = tribble(
    ['species', 'scientific_name'],
    'Adelie', 'Pygoscelis adeliae',
    'Gentoo', 'Pygoscelis papua',
    'Emperor', 'Aptenodytes forsteri',
);

echo $species |> inner_join($scientific, by: 'species');
echo "\n", $species |> left_join($scientific, by: 'species');
echo "\n", $scientific |> anti_join($species, by: 'species');
```

## PHP 8.3–8.4

```php
use function Phibble\tribble;

$species = phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->groupBy('species')
    ->summarize(avg_mass_g: mean('body_mass_g', naRm: true))
    ->get();

// A lookup table with one species that isn't in the data.
$scientific = tribble(
    ['species', 'scientific_name'],
    'Adelie', 'Pygoscelis adeliae',
    'Gentoo', 'Pygoscelis papua',
    'Emperor', 'Aptenodytes forsteri',
);

echo phplyr($species)->innerJoin($scientific, by: 'species');
echo "\n", phplyr($species)->leftJoin($scientific, by: 'species');
echo "\n", phplyr($scientific)->antiJoin($species, by: 'species');
```

:::

```{.text .output}
# A tibble: 2 × 3
  species avg_mass_g scientific_name   
  <chr>        <dbl> <chr>             
1 Adelie       3701. Pygoscelis adeliae
2 Gentoo       5076. Pygoscelis papua  
# A tibble: 3 × 3
  species   avg_mass_g scientific_name   
  <chr>          <dbl> <chr>             
1 Adelie         3701. Pygoscelis adeliae
2 Chinstrap      3733. <NA>              
3 Gentoo         5076. Pygoscelis papua  
# A tibble: 1 × 2
  species scientific_name     
  <chr>   <chr>               
1 Emperor Aptenodytes forsteri
```
