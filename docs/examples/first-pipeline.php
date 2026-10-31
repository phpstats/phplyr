<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{filter, group_by, summarize, arrange, head, desc, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

echo $schools |>
    filter(fn ($r) => $r->study_hours >= 5) |>
    group_by('school') |>
    summarize(students: n(), avg_score: mean('score'), sd_score: sd('score')) |>
    arrange(desc('avg_score')) |>
    head(5);
