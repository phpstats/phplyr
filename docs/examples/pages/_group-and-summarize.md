::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{n, mean, max};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo $penguins |>
    group_by('species') |>
    summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g', naRm: true),
        max_mass_g: max('body_mass_g', naRm: true),
    ) |>
    arrange(desc('avg_mass_g'));
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, max};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo phplyr($penguins)
    ->groupBy('species')
    ->summarize(
        penguins: n(),
        avg_mass_g: mean('body_mass_g', naRm: true),
        max_mass_g: max('body_mass_g', naRm: true),
    )
    ->arrange(desc('avg_mass_g'));
```

:::

```{.text .output}
# A tibble: 3 × 4
  species   penguins avg_mass_g max_mass_g
  <chr>        <int>      <dbl>      <int>
1 Gentoo         124      5076.       6300
2 Chinstrap       68      3733.       4800
3 Adelie         152      3701.       4775
```
