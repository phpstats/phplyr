<?php

// Inside a group, the second closure argument is the group's table, and
// vector helpers such as max() are evaluated per group.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    filter(fn ($r, $g) => $r->body_mass_g === max('body_mass_g', naRm: true)($g)) |>
    select('species', 'island', 'sex', 'body_mass_g');
