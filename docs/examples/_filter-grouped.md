::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Inside a group, the second closure argument is the group's table, and
// vector helpers such as max() are evaluated per group.
echo DataFrame::fromCsv("$data/penguins.csv") |>
    group_by('species') |>
    filter(fn ($r, $g) => $r->body_mass_g === max('body_mass_g', naRm: true)($g)) |>
    select('species', 'island', 'sex', 'body_mass_g');
```

## PHP 8.3–8.4

```php
// Inside a group, the second closure argument is the group's table, and
// vector helpers such as max() are evaluated per group.
echo phplyr(DataFrame::fromCsv("$data/penguins.csv"))
    ->groupBy('species')
    ->filter(fn ($r, $g) => $r->body_mass_g === max('body_mass_g', naRm: true)($g))
    ->select('species', 'island', 'sex', 'body_mass_g');
```

:::

```{.text .output}
# A tibble: 3 × 4
# Groups:   species [3]
  species   island sex   body_mass_g
  <chr>     <chr>  <chr>       <int>
1 Adelie    Biscoe male         4775
2 Gentoo    Biscoe male         6300
3 Chinstrap Dream  male         4800
```
