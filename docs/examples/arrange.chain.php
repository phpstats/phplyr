<?php

// Heaviest first within each species.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->select('species', 'island', 'sex', 'body_mass_g')
    ->arrange('species', desc('body_mass_g'));
