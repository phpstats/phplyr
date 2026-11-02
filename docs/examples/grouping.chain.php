<?php

$penguins = phplyr(DataFrame::fromCsv("$data/penguins.csv"))->groupBy('species')->get();

echo json_encode([
    'groups' => phplyr($penguins)->groupVars(),
    'n_groups' => phplyr($penguins)->nGroups(),
    'sizes' => phplyr($penguins)->groupSize(),
]), "\n\n";

// Grouped mutate: helpers are evaluated within each group.
echo phplyr($penguins)
    ->mutate(centered: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), rank_in_species: min_rank(desc('body_mass_g')))
    ->ungroup()
    ->select('species', 'body_mass_g', 'centered', 'rank_in_species')
    ->head(4);

// One table per group.
$parts = phplyr($penguins)->groupSplit();
echo "\n", \count($parts), ' tables; the first has ', $parts[0]->nrow(), " rows\n";
