::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{filter, group_by, summarize};
use function Phplyr\{arrange, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |>
    filter(fn ($r) => $r->body_mass_g !== null) |>
    group_by('species') |>
    summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    ) |>
    arrange(desc('avg_mass_g'));
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)
    ->filter(fn ($r) => $r->body_mass_g !== null)
    ->groupBy('species')
    ->summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g'),
        sd_mass_g: sd('body_mass_g'),
    )
    ->arrange(desc('avg_mass_g'));
```

:::

```{.text .output}
# A tibble: 3 × 4
  species   penguins avg_mass_g sd_mass_g
  <chr>        <int>      <dbl>     <dbl>
1 Gentoo         123      5076.      504.
2 Chinstrap       68      3733.      384.
3 Adelie         151      3701.      459.
```
