<?php

require 'vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{group_by, summarize, arrange, desc, n, sum, mean};

$sales = DataFrame::fromRows([
    ['region' => 'North', 'product' => 'pen', 'units' => 12],
    ['region' => 'North', 'product' => 'stapler', 'units' => 3],
    ['region' => 'South', 'product' => 'pen', 'units' => 20],
    ['region' => 'South', 'product' => 'notebook', 'units' => 7],
    ['region' => 'South', 'product' => 'stapler', 'units' => null],
    ['region' => 'West', 'product' => 'notebook', 'units' => 9],
]);

echo $sales |>
    group_by('region') |>
    summarize(orders: n(), total_units: sum('units', naRm: true), avg_units: mean('units', naRm: true)) |>
    arrange(desc('total_units'));
