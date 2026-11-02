::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{count, pivot_longer, pivot_wider};
use function Phplyr\starts_with;
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
$wide = $penguins |>
    count('species', 'year') |>
    pivot_wider(
        namesFrom: 'year',
        valuesFrom: 'n',
        namesPrefix: 'n_',
    );
echo $wide, "\n\n";
echo $wide |> pivot_longer(
    starts_with('n_'),
    namesTo: 'year',
    namesPrefix: 'n_',
    valuesTo: 'penguins',
);
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, starts_with};
$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");
$wide = phplyr($penguins)
    ->count('species', 'year')
    ->pivotWider(
        namesFrom: 'year',
        valuesFrom: 'n',
        namesPrefix: 'n_',
    )
    ->get();
echo $wide, "\n\n";
echo phplyr($wide)->pivotLonger(
    starts_with('n_'),
    namesTo: 'year',
    namesPrefix: 'n_',
    valuesTo: 'penguins',
);
```

:::

```{.text .output}
# A tibble: 3 × 4
  species   n_2007 n_2008 n_2009
  <chr>      <int>  <int>  <int>
1 Adelie        50     50     52
2 Chinstrap     26     18     24
3 Gentoo        34     46     44

# A tibble: 9 × 3
  species   year  penguins
  <chr>     <chr>    <int>
1 Adelie    2007        50
2 Adelie    2008        50
3 Adelie    2009        52
4 Chinstrap 2007        26
5 Chinstrap 2008        18
6 Chinstrap 2009        24
7 Gentoo    2007        34
8 Gentoo    2008        46
9 Gentoo    2009        44
```
