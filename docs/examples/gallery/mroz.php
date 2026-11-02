<?php

// Labor-force participation by age quartile, then a
// logistic regression.
$mroz = DataFrame::fromCsv("$data/mroz.csv") |>
    mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0));

echo $mroz |>
    mutate(age_quartile: ntile('age', 4)) |>
    group_by('age_quartile') |>
    summarize(
        women: n(),
        min_age: min('age'),
        max_age: max('age'),
        participation: mean('in_lf'),
    );

$fit = $mroz |> glm(
    'in_lf ~ age + k5 + k618 + inc',
    family: 'binomial',
);
echo "\n\nOdds ratios\n";
$terms = array_slice($fit['columns'], 1, null, true);
foreach ($terms as $i => $term) {
    $ratio = exp($fit['coefficients'][$i]);
    printf("%-5s %.3f\n", $term, $ratio);
}
