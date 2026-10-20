<?php

$schools = phplyr(DataFrame::fromCsv('schools.csv'))->groupBy('school')->get();

// The two best students in each of the first three schools.
echo phplyr($schools)
    ->sliceMax('score', n: 2)
    ->filter(fn ($r) => $r->school <= 'school-03')
    ->select('school', 'score');
