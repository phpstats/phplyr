::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{pivot_longer, pivot_wider, starts_with};

$wide = DataFrame::fromRows([
    ['store' => 'Leeds', 'sales_2023' => 120, 'sales_2024' => 135, 'sales_2025' => 150],
    ['store' => 'Lyon', 'sales_2023' => 95, 'sales_2024' => 110, 'sales_2025' => 104],
]);
echo $wide, "\n\n";

$long = $wide |>
    pivot_longer(starts_with('sales_'), namesTo: 'year', namesPrefix: 'sales_', valuesTo: 'sales');
echo $long, "\n\n";

echo $long |>
    pivot_wider(namesFrom: 'year', valuesFrom: 'sales', namesPrefix: 'sales_');
```

## PHP 8.3–8.4

```php
<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, starts_with};

$wide = DataFrame::fromRows([
    ['store' => 'Leeds', 'sales_2023' => 120, 'sales_2024' => 135, 'sales_2025' => 150],
    ['store' => 'Lyon', 'sales_2023' => 95, 'sales_2024' => 110, 'sales_2025' => 104],
]);
echo $wide, "\n\n";

$long = phplyr($wide)
    ->pivotLonger(starts_with('sales_'), namesTo: 'year', namesPrefix: 'sales_', valuesTo: 'sales')
    ->get();
echo $long, "\n\n";

echo phplyr($long)
    ->pivotWider(namesFrom: 'year', valuesFrom: 'sales', namesPrefix: 'sales_');
```

:::

```{.text .output}
# A tibble: 2 × 4
  store sales_2023 sales_2024 sales_2025
  <chr>      <int>      <int>      <int>
1 Leeds        120        135        150
2 Lyon          95        110        104

# A tibble: 6 × 3
  store year  sales
  <chr> <chr> <int>
1 Leeds 2023    120
2 Leeds 2024    135
3 Leeds 2025    150
4 Lyon  2023     95
5 Lyon  2024    110
6 Lyon  2025    104

# A tibble: 2 × 4
  store sales_2023 sales_2024 sales_2025
  <chr>      <int>      <int>      <int>
1 Leeds        120        135        150
2 Lyon          95        110        104
```
