<?php

$penguins = DataFrame::fromCsv("$data/penguins.csv") |> group_by('species');

echo json_encode([
    'groups' => $penguins |> group_vars(),
    'n_groups' => $penguins |> n_groups(),
    'sizes' => $penguins |> group_size(),
]), "\n\n";

// Grouped mutate: helpers are evaluated within each group.
echo $penguins |>
    mutate(centered: col('body_mass_g')->sub(mean('body_mass_g', naRm: true)), rank_in_species: min_rank(desc('body_mass_g'))) |>
    ungroup() |>
    select('species', 'body_mass_g', 'centered', 'rank_in_species') |>
    head(4);

// One table per group.
$parts = $penguins |> group_split();
echo "\n", \count($parts), ' tables; the first has ', $parts[0]->nrow(), " rows\n";
