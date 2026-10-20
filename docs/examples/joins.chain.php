<?php

$schools = phplyr(DataFrame::fromCsv('schools.csv'))
    ->groupBy('school')
    ->summarise(avg_score: mean('score'))
    ->get();

$districts = DataFrame::fromRows([
    ['school_id' => 'school-01', 'district' => 'North'],
    ['school_id' => 'school-02', 'district' => 'North'],
    ['school_id' => 'school-03', 'district' => 'South'],
    ['school_id' => 'school-99', 'district' => 'East'],
]);

echo phplyr($schools)->innerJoin($districts, by: ['school' => 'school_id']);
echo "\n", phplyr($schools)->leftJoin($districts, by: ['school' => 'school_id'])->head(5);
echo "\n", phplyr($districts)->antiJoin($schools, by: ['school_id' => 'school']);
