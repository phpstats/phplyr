::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{filter, mutate, if_else, col};

$orders = DataFrame::fromRows([
    ['order' => 1, 'product' => 'pen', 'qty' => 10, 'price' => 1.50],
    ['order' => 2, 'product' => 'notebook', 'qty' => 0, 'price' => 4.00],
    ['order' => 3, 'product' => 'stapler', 'qty' => 2, 'price' => 12.00],
    ['order' => 4, 'product' => 'pen', 'qty' => 40, 'price' => 1.25],
]);

echo $orders |>
    filter(fn ($r) => $r->qty > 0) |>
    mutate(
        total: fn ($r) => $r->qty * $r->price,
        size: if_else(col('qty')->ge(20), 'bulk', 'single'),
    );
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, if_else, col};

$orders = DataFrame::fromRows([
    ['order' => 1, 'product' => 'pen', 'qty' => 10, 'price' => 1.50],
    ['order' => 2, 'product' => 'notebook', 'qty' => 0, 'price' => 4.00],
    ['order' => 3, 'product' => 'stapler', 'qty' => 2, 'price' => 12.00],
    ['order' => 4, 'product' => 'pen', 'qty' => 40, 'price' => 1.25],
]);

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
