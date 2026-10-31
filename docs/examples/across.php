<?php

$schools = DataFrame::fromCsv('schools.csv') |> group_by('school');

// Phplyr helpers can be passed by name; each gets the column name.
echo $schools |>
    summarize(across(where('numeric'), ['avg' => mean(...), 'sd' => sd(...)])) |>
    head(3);

// Other closures get the column's values (summarize) or each value (mutate).
echo "\n", $schools |>
    summarize(across(['study_hours', 'score'], fn (array $v) => round(\max($v) - \min($v), 1), names: 'range_{col}')) |>
    head(3);
echo "\n", $schools |> ungroup() |> mutate(across(ends_with('score'), fn ($v) => round($v))) |> head(3);
