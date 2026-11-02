::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo $penguins |> distinct('species', 'island') |> arrange('species', 'island');
echo "\n", json_encode($penguins |> head(5) |> pull('body_mass_g')), "\n";
echo json_encode($penguins |> head(5) |> pull(-1)), "\n";
```

## PHP 8.3–8.4

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo phplyr($penguins)->distinct('species', 'island')->arrange('species', 'island');
echo "\n", json_encode(phplyr($penguins)->head(5)->pull('body_mass_g')), "\n";
echo json_encode(phplyr($penguins)->head(5)->pull(-1)), "\n";
```

:::

```{.text .output}
# A tibble: 5 × 2
  species   island   
  <chr>     <chr>    
1 Adelie    Biscoe   
2 Adelie    Dream    
3 Adelie    Torgersen
4 Chinstrap Dream    
5 Gentoo    Biscoe   
[3750,3800,3250,null,3450]
[2007,2007,2007,2007,2007]
```
