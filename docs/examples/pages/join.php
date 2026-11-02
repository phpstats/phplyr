<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\{filter, select, left_join};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$weather = DataFrame::fromCsv("$data/weather.csv");
$airports = DataFrame::fromCsv("$data/airports.csv");
$nineAm = fn ($r) => $r->month === 1 && $r->day === 1
    && $r->hour === 9;
echo $weather |>
    filter($nineAm) |>
    select('origin', 'time_hour', 'temp', 'wind_speed') |>
    left_join(
        $airports |> select('faa', 'name', 'alt'),
        by: ['origin' => 'faa'],
        relationship: 'many-to-one',
    );
