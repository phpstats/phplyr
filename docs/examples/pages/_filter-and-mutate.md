::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use function Phibble\tribble;
use function Phplyr\{filter, mutate, if_else, col};

$orders = tribble(
    ['order', 'product', 'qty', 'price'],
    1, 'pen', 10, 1.50,
    2, 'notebook', 0, 4.00,
    3, 'stapler', 2, 12.00,
    4, 'pen', 40, 1.25,
);

echo $orders |>
    filter(fn ($r) => $r->qty > 0) |>
    mutate(
        total: fn ($r) => $r->qty * $r->price,
        size: if_else(col('qty')->ge(20), 'bulk', 'single'),
    );
```

## PHP 8.3–8.4

```php
use function Phibble\tribble;
use function Phplyr\{phplyr, if_else, col};

$orders = tribble(
    ['order', 'product', 'qty', 'price'],
    1, 'pen', 10, 1.50,
    2, 'notebook', 0, 4.00,
    3, 'stapler', 2, 12.00,
    4, 'pen', 40, 1.25,
);

echo phplyr($orders)
    ->filter(fn ($r) => $r->qty > 0)
    ->mutate(
        total: fn ($r) => $r->qty * $r->price,
        size: if_else(col('qty')->ge(20), 'bulk', 'single'),
    );
```

:::

```{.text .output}
# A tibble: 3 × 6
  order product   qty price total size  
  <int> <chr>   <int> <dbl> <dbl> <chr> 
1     1 pen        10  1.5     15 single
2     3 stapler     2 12       24 single
3     4 pen        40  1.25    50 bulk
```
