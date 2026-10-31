::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\left_join;

$orders = DataFrame::fromRows([
    ['order' => 1, 'customer_id' => 'c1', 'amount' => 25.00],
    ['order' => 2, 'customer_id' => 'c2', 'amount' => 12.50],
    ['order' => 3, 'customer_id' => 'c1', 'amount' => 8.00],
    ['order' => 4, 'customer_id' => 'c9', 'amount' => 40.00],
]);
$customers = DataFrame::fromRows([
    ['customer_id' => 'c1', 'name' => 'Ana', 'city' => 'Lisbon'],
    ['customer_id' => 'c2', 'name' => 'Ben', 'city' => 'Leeds'],
    ['customer_id' => 'c3', 'name' => 'Cleo', 'city' => 'Lyon'],
]);

echo $orders |> left_join($customers, by: 'customer_id');
```

## PHP 8.3–8.4

```php
<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\phplyr;

$orders = DataFrame::fromRows([
    ['order' => 1, 'customer_id' => 'c1', 'amount' => 25.00],
    ['order' => 2, 'customer_id' => 'c2', 'amount' => 12.50],
    ['order' => 3, 'customer_id' => 'c1', 'amount' => 8.00],
    ['order' => 4, 'customer_id' => 'c9', 'amount' => 40.00],
]);
$customers = DataFrame::fromRows([
    ['customer_id' => 'c1', 'name' => 'Ana', 'city' => 'Lisbon'],
    ['customer_id' => 'c2', 'name' => 'Ben', 'city' => 'Leeds'],
    ['customer_id' => 'c3', 'name' => 'Cleo', 'city' => 'Lyon'],
]);

echo phplyr($orders)->leftJoin($customers, by: 'customer_id');
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
