::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Fuel economy, power, and weight by number of cylinders,
// and each car's mpg against its cylinder group's mean.
$cars = DataFrame::fromCsv("$data/mtcars.csv") |>
    rename(model: 'rownames');

echo $cars |>
    group_by('cyl') |>
    summarize(
        across(
            ['mpg', 'hp', 'wt'],
            ['mean' => mean(...), 'max' => max(...)],
        ),
        cars: n(),
    ) |>
    mutate(across(
        where('double'),
        fn ($v) => round($v, 1),
    ));

$vsGroup = fn ($r, $g) => round(
    $r->mpg - mean('mpg')($g),
    1,
);
echo "\n\n", $cars |>
    select('model', 'cyl', 'mpg') |>
    mutate(vs_group: $vsGroup, _by: 'cyl') |>
    arrange('cyl', desc('vs_group'));
```

## PHP 8.3–8.4

```php
// Fuel economy, power, and weight by number of cylinders,
// and each car's mpg against its cylinder group's mean.
$cars = phplyr(DataFrame::fromCsv("$data/mtcars.csv"))
    ->rename(model: 'rownames')
    ->get();

echo phplyr($cars)
    ->groupBy('cyl')
    ->summarize(
        across(
            ['mpg', 'hp', 'wt'],
            ['mean' => mean(...), 'max' => max(...)],
        ),
        cars: n(),
    )
    ->mutate(across(
        where('double'),
        fn ($v) => round($v, 1),
    ));

$vsGroup = fn ($r, $g) => round(
    $r->mpg - mean('mpg')($g),
    1,
);
echo "\n\n", phplyr($cars)
    ->select('model', 'cyl', 'mpg')
    ->mutate(vs_group: $vsGroup, _by: 'cyl')
    ->arrange('cyl', desc('vs_group'));
```

:::

```{.text .output}
# A tibble: 3 × 8
    cyl mpg_mean mpg_max hp_mean hp_max wt_mean wt_max  cars
  <int>    <dbl>   <dbl>   <dbl>  <int>   <dbl>  <dbl> <int>
1     4     26.7    33.9    82.6    113     2.3    3.2    11
2     6     19.7    21.4   122.     175     3.1    3.5     7
3     8     15.1    19.2   209.     335     4      5.4    14

# A tibble: 32 × 4
   model            cyl   mpg vs_group
   <chr>          <int> <dbl>    <dbl>
 1 Toyota Corolla     4  33.9      7.2
 2 Fiat 128           4  32.4      5.7
 3 Honda Civic        4  30.4      3.7
 4 Lotus Europa       4  30.4      3.7
 5 Fiat X1-9          4  27.3      0.6
 6 Porsche 914-2      4  26       -0.7
 7 Merc 240D          4  24.4     -2.3
 8 Datsun 710         4  22.8     -3.9
 9 Merc 230           4  22.8     -3.9
10 Toyota Corona      4  21.5     -5.2
# ℹ 22 more rows
```
