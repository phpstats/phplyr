<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo implode(', ', (phplyr($penguins)->select('species', ends_with('_mm'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(except('rownames'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(where('numeric'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(['mass' => 'body_mass_g'], 'species')->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->relocate('sex', _before: 'island')->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->renameWith(strtoupper(...), contains('length'))->get())->columns()), "\n";
