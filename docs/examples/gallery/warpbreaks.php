<?php

// Mean breaks by wool and tension, as a cross-tabulation,
// then a Poisson regression.
$looms = DataFrame::fromCsv("$data/warpbreaks.csv")
    ->asFactor('tension', ['L', 'M', 'H']);

echo $looms |>
    group_by('wool', 'tension') |>
    summarize(
        mean_breaks: mean('breaks'),
        _groups: 'drop',
    ) |>
    mutate(
        mean_breaks: fn ($r) => round($r->mean_breaks, 1),
    ) |>
    pivot_wider(
        namesFrom: 'tension',
        valuesFrom: 'mean_breaks',
    );

$fit = $looms |> glm(
    'breaks ~ wool + tension',
    family: 'poisson',
);
echo "\n\nRate ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-8s %.3f\n", $term, $ratio);
}
