<?php

// Mean breaks by wool and tension, as a cross-tabulation,
// then a Poisson regression.
$looms = DataFrame::fromCsv("$data/warpbreaks.csv")
    ->asFactor('tension', ['L', 'M', 'H']);

echo phplyr($looms)
    ->groupBy('wool', 'tension')
    ->summarize(
        mean_breaks: mean('breaks'),
        _groups: 'drop',
    )
    ->mutate(
        mean_breaks: fn ($r) => round($r->mean_breaks, 1),
    )
    ->pivotWider(
        namesFrom: 'tension',
        valuesFrom: 'mean_breaks',
    );

$fit = phplyr($looms)->glm(
    'breaks ~ wool + tension',
    family: 'poisson',
);
echo "\n\nRate ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-8s %.3f\n", $term, $ratio);
}
