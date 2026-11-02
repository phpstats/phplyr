::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
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
```

## PHP 8.3–8.4

```php
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
```

:::

```{.text .output}
# A tibble: 3 × 6
  origin time_hour            temp wind_speed name                  alt
  <chr>  <dttm>              <dbl>      <dbl> <chr>               <int>
1 EWR    2013-01-01 14:00:00  39.9       15.0 Newark Liberty Intl    18
2 JFK    2013-01-01 14:00:00  39.9       16.1 John F Kennedy Intl    13
3 LGA    2013-01-01 14:00:00  39.9       18.4 La Guardia             22
```
