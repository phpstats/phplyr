<?php

require __DIR__.'/vendor/autoload.php';

use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

echo phplyr($schools)
    ->filter(fn ($r) => $r->study_hours >= 5)
    ->groupBy('school')
    ->summarize(
        students: n(),
        avg_score: mean('score'),
        sd_score: sd('score'),
    )
    ->arrange(desc('avg_score'))
    ->head(5);
