<?php

require 'vendor/autoload.php';

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
