<?php

$fit = phplyr(DataFrame::fromCsv("$data/mtcars.csv"))
    ->lm('mpg ~ wt + hp');

foreach ($fit['columns'] as $i => $term) {
    printf("%-12s %8.4f  (se %.4f)\n", $term, $fit['coefficients'][$i], $fit['std_errors'][$i]);
}
printf("R² = %.4f, n = %d\n", $fit['r_squared'], $fit['n_observations']);

$logit = phplyr(DataFrame::fromCsv("$data/mroz.csv"))
    ->mutate(in_lf: if_else(col('lfp')->eq('yes'), 1, 0))
    ->glm('in_lf ~ age + k5 + inc', family: 'binomial');
echo 'logit coefficients: ', implode(', ', array_map(fn ($b) => round($b, 4), $logit['coefficients'])), "\n";
