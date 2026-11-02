<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na() |>
    group_by('species');

// Phplyr helpers can be passed by name; each gets the column name.
echo $penguins |>
    summarize(across(ends_with('_mm'), ['avg' => mean(...), 'sd' => sd(...)]));

// Other closures get the column's values (summarize) or each value (mutate).
echo "\n", $penguins |>
    summarize(across(['flipper_length_mm', 'body_mass_g'], fn (array $v) => \max($v) - \min($v), names: 'range_{col}'));
echo "\n", $penguins |> ungroup() |> mutate(across(ends_with('_mm'), fn ($v) => round($v))) |> head(3);
