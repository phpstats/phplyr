<?php
require 'vendor/autoload.php';
use Phibble\DataFrame;
use function Phplyr\phplyr;
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$weather = DataFrame::fromCsv("$data/weather.csv");
$airports = DataFrame::fromCsv("$data/airports.csv");
$nineAm = fn ($r) => $r->month === 1 && $r->day === 1
    && $r->hour === 9;
$names = phplyr($airports)
    ->select('faa', 'name', 'alt')
    ->get();
echo phplyr($weather)
    ->filter($nineAm)
    ->select('origin', 'time_hour', 'temp', 'wind_speed')
    ->leftJoin(
        $names,
        by: ['origin' => 'faa'],
        relationship: 'many-to-one',
    );
