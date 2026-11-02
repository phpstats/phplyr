::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |> count('species', 'island', sort: true);
echo "\n", $penguins |> count('species', wt: 'body_mass_g', name: 'total_mass_g');
```

## PHP 8.3–8.4

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)->count('species', 'island', sort: true);
echo "\n", phplyr($penguins)->count('species', wt: 'body_mass_g', name: 'total_mass_g');
```

:::

```{.text .output}
# A tibble: 5 × 3
  species   island        n
  <chr>     <chr>     <int>
1 Gentoo    Biscoe      124
2 Chinstrap Dream        68
3 Adelie    Dream        56
4 Adelie    Torgersen    52
5 Adelie    Biscoe       44
# A tibble: 3 × 2
  species   total_mass_g
  <chr>            <int>
1 Adelie          558800
2 Chinstrap       253850
3 Gentoo          624350
```
