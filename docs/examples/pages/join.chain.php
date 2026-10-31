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
