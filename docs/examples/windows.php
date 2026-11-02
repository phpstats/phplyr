<?php

// Female Chinstrap penguins, from the shortest flippers up.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    filter(fn ($r) => $r->species === 'Chinstrap' && $r->sex === 'female') |>
    arrange('flipper_length_mm') |>
    mutate(
        prev: lag('body_mass_g'),
        change: lag('body_mass_g')->map(fn ($prev, $r) => $prev === null ? null : $r->body_mass_g - $prev),
        running_mean: cummean('body_mass_g'),
        rank: min_rank(desc('body_mass_g')),
        pct: percent_rank('body_mass_g'),
        quartile: ntile('body_mass_g', 4),
    ) |>
    select('flipper_length_mm', 'body_mass_g', 'prev', 'change', 'running_mean', 'rank', 'pct', 'quartile') |>
    head(8);
