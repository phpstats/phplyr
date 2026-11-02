<?php

// Heaviest first within each species.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    select('species', 'island', 'sex', 'body_mass_g') |>
    arrange('species', desc('body_mass_g'));
