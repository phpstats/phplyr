::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{filter, select, mutate, if_else, col};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo $penguins |>
    filter(fn ($r) => $r->body_mass_g !== null) |>
    select('species', 'island', 'sex', 'body_mass_g') |>
    mutate(
        mass_kg: fn ($r) => $r->body_mass_g / 1000,
        size: if_else(
            col('body_mass_g')->ge(4500),
            'large',
            'small',
        ),
    );
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, if_else, col};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
echo phplyr($penguins)
    ->filter(fn ($r) => $r->body_mass_g !== null)
    ->select('species', 'island', 'sex', 'body_mass_g')
    ->mutate(
        mass_kg: fn ($r) => $r->body_mass_g / 1000,
        size: if_else(
            col('body_mass_g')->ge(4500),
            'large',
            'small',
        ),
    );
```

:::

```{.text .output}
# A tibble: 342 × 6
   species island    sex    body_mass_g mass_kg size 
   <chr>   <chr>     <chr>        <int>   <dbl> <chr>
 1 Adelie  Torgersen male          3750    3.75 small
 2 Adelie  Torgersen female        3800    3.8  small
 3 Adelie  Torgersen female        3250    3.25 small
 4 Adelie  Torgersen female        3450    3.45 small
 5 Adelie  Torgersen male          3650    3.65 small
 6 Adelie  Torgersen female        3625    3.62 small
 7 Adelie  Torgersen male          4675    4.68 large
 8 Adelie  Torgersen <NA>          3475    3.48 small
 9 Adelie  Torgersen <NA>          4250    4.25 small
10 Adelie  Torgersen <NA>          3300    3.3  small
# ℹ 332 more rows
```
