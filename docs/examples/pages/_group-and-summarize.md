::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use function Phibble\tribble;
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{n, sum, mean};

$sales = tribble(
    ['region', 'product', 'units'],
    'North', 'pen', 12,
    'North', 'stapler', 3,
    'South', 'pen', 20,
    'South', 'notebook', 7,
    'South', 'stapler', null,
    'West', 'notebook', 9,
);

echo $sales |>
    group_by('region') |>
    summarize(
        orders: n(),
        total_units: sum('units', naRm: true),
        avg_units: mean('units', naRm: true),
    ) |>
    arrange(desc('total_units'));
```

## PHP 8.3–8.4

```php
use function Phibble\tribble;
use function Phplyr\{phplyr, desc, n, sum, mean};

$sales = tribble(
    ['region', 'product', 'units'],
    'North', 'pen', 12,
    'North', 'stapler', 3,
    'South', 'pen', 20,
    'South', 'notebook', 7,
    'South', 'stapler', null,
    'West', 'notebook', 9,
);

echo phplyr($sales)
    ->groupBy('region')
    ->summarize(
        orders: n(),
        total_units: sum('units', naRm: true),
        avg_units: mean('units', naRm: true),
    )
    ->arrange(desc('total_units'));
```

:::

```{.text .output}
# A tibble: 3 × 4
  region orders total_units avg_units
  <chr>   <int>       <int>     <dbl>
1 South       3          27      13.5
2 North       2          15       7.5
3 West        1           9       9
```
