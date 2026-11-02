<?php

// The two heaviest penguins of each species.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    slice_max('body_mass_g', n: 2) |>
    select('species', 'island', 'sex', 'body_mass_g');
