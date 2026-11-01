::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use function Phibble\tribble;
use function Phplyr\left_join;

$orders = tribble(
    ['order', 'customer_id', 'amount'],
    1, 'c1', 25.00,
    2, 'c2', 12.50,
    3, 'c1', 8.00,
    4, 'c9', 40.00,
);
$customers = tribble(
    ['customer_id', 'name', 'city'],
    'c1', 'Ana', 'Lisbon',
    'c2', 'Ben', 'Leeds',
    'c3', 'Cleo', 'Lyon',
);

echo $orders |> left_join($customers, by: 'customer_id');
```

## PHP 8.3–8.4

```php
use function Phibble\tribble;
use function Phplyr\phplyr;

$orders = tribble(
    ['order', 'customer_id', 'amount'],
    1, 'c1', 25.00,
    2, 'c2', 12.50,
    3, 'c1', 8.00,
    4, 'c9', 40.00,
);
$customers = tribble(
    ['customer_id', 'name', 'city'],
    'c1', 'Ana', 'Lisbon',
    'c2', 'Ben', 'Leeds',
    'c3', 'Cleo', 'Lyon',
);

echo phplyr($orders)
    ->leftJoin($customers, by: 'customer_id');
```

:::

```{.text .output}
# A tibble: 4 × 5
  order customer_id amount name  city  
  <int> <chr>        <dbl> <chr> <chr> 
1     1 c1            25   Ana   Lisbon
2     2 c2            12.5 Ben   Leeds 
3     3 c1             8   Ana   Lisbon
4     4 c9            40   <NA>  <NA>
```
