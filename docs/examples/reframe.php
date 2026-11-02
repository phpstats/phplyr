<?php

echo DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na('body_mass_g') |>
    group_by('species') |>
    reframe(
        p: [0.25, 0.5, 0.75],
        body_mass_g: fn ($g) => array_map(fn ($p) => Stats::quantile($g->column('body_mass_g'), $p), [0.25, 0.5, 0.75]),
    );
