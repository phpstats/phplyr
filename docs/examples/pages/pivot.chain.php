<?php

require 'vendor/autoload.php';

use function Phibble\tribble;
use function Phplyr\{phplyr, starts_with};

$wide = tribble(
    ['store', 'sales_2023', 'sales_2024', 'sales_2025'],
    'Leeds', 120, 135, 150,
    'Lyon', 95, 110, 104,
);
echo $wide, "\n\n";

$long = phplyr($wide)
    ->pivotLonger(
        starts_with('sales_'),
        namesTo: 'year',
        namesPrefix: 'sales_',
        valuesTo: 'sales',
    )
    ->get();
echo $long, "\n\n";

echo phplyr($long)->pivotWider(
    namesFrom: 'year',
    valuesFrom: 'sales',
    namesPrefix: 'sales_',
);
