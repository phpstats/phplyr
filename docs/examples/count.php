<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |> count('species', 'island', sort: true);
echo "\n", $penguins |> count('species', wt: 'body_mass_g', name: 'total_mass_g');
