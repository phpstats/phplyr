<?php

echo DataFrame::fromCsv("$data/penguins.csv") |>
    drop_na() |>
    select('species', 'bill_length_mm', 'bill_depth_mm', 'flipper_length_mm', 'body_mass_g') |>
    mutate(
        bill_ratio: fn ($r) => round($r->bill_length_mm / $r->bill_depth_mm, 2),
        flipper: if_else(col('flipper_length_mm')->ge(200), 'long', 'short'),
        rank: min_rank(desc('body_mass_g')),
    ) |>
    arrange('rank') |>
    head(6);
