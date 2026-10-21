<?php

// Calls by staffing level and visitor volume, as a cross-tabulation.
echo phplyr(DataFrame::fromCsv('clinic.csv'))
    ->mutate(
        staffing: case_when([fn ($r) => $r->staff < 4, 'low'], [fn ($r) => $r->staff < 6, 'mid'], default: 'high'),
        volume: if_else(fn ($r) => $r->visitors >= 250, 'busy', 'quiet'),
    )
    ->groupBy('staffing', 'volume')
    ->summarise(mean_calls: mean('calls'), _groups: 'drop')
    ->mutate(mean_calls: fn ($r) => round($r->mean_calls, 2))
    ->pivotWider(namesFrom: 'volume', valuesFrom: 'mean_calls', namesSort: true)
    ->arrange(fn ($r) => array_search($r->staffing, ['low', 'mid', 'high']));
