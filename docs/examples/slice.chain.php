<?php

// The two heaviest penguins of each species.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->groupBy('species')
    ->sliceMax('body_mass_g', n: 2)
    ->select('species', 'island', 'sex', 'body_mass_g');
