::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Inside a group, the second closure argument is the group's table, and
// vector helpers such as max() are evaluated per group.
echo DataFrame::fromCsv('schools.csv') |>
    group_by('school') |>
    filter(fn ($r, $g) => $r->score === max('score')($g)) |>
    select('school', 'score') |>
    head(4);
```

## PHP 8.3–8.4

```php
// Inside a group, the second closure argument is the group's table, and
// vector helpers such as max() are evaluated per group.
echo phplyr(DataFrame::fromCsv('schools.csv'))
    ->groupBy('school')
    ->filter(fn ($r, $g) => $r->score === max('score')($g))
    ->select('school', 'score')
    ->head(4);
```

:::

```{.text .output}
# A tibble: 4 × 2
# Groups:   school [4]
  school    score
  <chr>     <dbl>
1 school-01  91.0
2 school-02  92.1
3 school-03  84.0
4 school-04  83.9
```
