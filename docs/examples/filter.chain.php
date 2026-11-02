<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Several conditions are combined with AND; NA counts as false.
echo phplyr($penguins)
    ->filter(fn ($r) => $r->bill_length_mm > 50, between('flipper_length_mm', 190, 200))
    ->select('species', 'island', 'bill_length_mm', 'flipper_length_mm');

// filter_out() drops the rows where every condition is true.
echo "\n", phplyr($penguins)->filterOut(fn ($r) => $r->species === 'Adelie')->count();
