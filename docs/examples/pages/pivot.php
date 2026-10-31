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
