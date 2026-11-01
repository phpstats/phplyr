<?php

require 'vendor/autoload.php';

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
