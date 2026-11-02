<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)->distinct('species', 'island')->arrange('species', 'island');
echo "\n", json_encode(phplyr($penguins)->head(5)->pull('body_mass_g')), "\n";
echo json_encode(phplyr($penguins)->head(5)->pull(-1)), "\n";
