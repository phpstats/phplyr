<?php

$schools = phplyr(DataFrame::fromCsv('schools.csv'))->groupBy('school')->get();

echo json_encode([
    'groups' => phplyr($schools)->groupVars(),
    'n_groups' => phplyr($schools)->nGroups(),
    'sizes' => \array_slice(phplyr($schools)->groupSize(), 0, 4),
]), "\n\n";

// Grouped mutate: helpers are evaluated within each group.
echo phplyr($schools)
    ->mutate(centered: fn ($r, $g) => round($r->score - mean('score')($g), 2), rank_in_school: min_rank(desc('score')))
    ->ungroup()
    ->head(4);

// One table per group.
$parts = phplyr($schools)->groupSplit();
echo "\n", \count($parts), ' tables; the first has ', $parts[0]->nrow(), " rows\n";
