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
