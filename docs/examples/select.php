<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo implode(', ', ($penguins |> select('species', ends_with('_mm')))->columns()), "\n";
echo implode(', ', ($penguins |> select(except('rownames')))->columns()), "\n";
echo implode(', ', ($penguins |> select(where('numeric')))->columns()), "\n";
echo implode(', ', ($penguins |> select(['mass' => 'body_mass_g'], 'species'))->columns()), "\n";
echo implode(', ', ($penguins |> relocate('sex', _before: 'island'))->columns()), "\n";
echo implode(', ', ($penguins |> rename_with(strtoupper(...), contains('length')))->columns()), "\n";
