<?php

echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->summarize(
        penguins: n(),
        species: n_distinct('species'),
        median_mass_g: median('body_mass_g', naRm: true),
        q90_flipper_mm: quantile('flipper_length_mm', 0.9, naRm: true),
        iqr_bill_mm: iqr('bill_length_mm', naRm: true),
        heaviest: fn ($g) => phplyr($g)->sliceMax('body_mass_g', n: 1)->pull('species')[0],
    );
