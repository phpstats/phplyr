<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |> distinct('species', 'island') |> arrange('species', 'island');
echo "\n", json_encode($penguins |> head(5) |> pull('body_mass_g')), "\n";
echo json_encode($penguins |> head(5) |> pull(-1)), "\n";
