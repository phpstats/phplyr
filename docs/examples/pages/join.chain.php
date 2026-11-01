<?php

require 'vendor/autoload.php';

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
