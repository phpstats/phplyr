::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
echo DataFrame::fromCsv('schools.csv') |>
    summarize(
        students: n(),
        schools: n_distinct('school'),
        median_hours: median('study_hours'),
        q90_score: quantile('score', 0.9),
        iqr_score: iqr('score'),
        top_school: fn ($g) => ($g |> slice_max('score', n: 1) |> pull('school'))[0],
    );
```

## PHP 8.3–8.4

```php
echo phplyr(DataFrame::fromCsv('schools.csv'))
    ->summarize(
        students: n(),
        schools: n_distinct('school'),
        median_hours: median('study_hours'),
        q90_score: quantile('score', 0.9),
        iqr_score: iqr('score'),
        top_school: fn ($g) => (phplyr($g)->sliceMax('score', n: 1)->pull('school'))[0],
    );
```

:::

```{.text .output}
# A tibble: 1 × 6
  students schools median_hours q90_score iqr_score top_school
     <int>   <int>        <dbl>     <dbl>     <dbl> <chr>     
1      250      12         6.66      86.1      15.3 school-06
```
