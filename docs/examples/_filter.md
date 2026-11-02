::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Several conditions are combined with AND; NA counts as false.
echo $penguins |>
    filter(fn ($r) => $r->bill_length_mm > 50, between('flipper_length_mm', 190, 200)) |>
    select('species', 'island', 'bill_length_mm', 'flipper_length_mm');

// filter_out() drops the rows where every condition is true.
echo "\n", $penguins |> filter_out(fn ($r) => $r->species === 'Adelie') |> count();
```

## PHP 8.3–8.4

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Several conditions are combined with AND; NA counts as false.
echo phplyr($penguins)
    ->filter(fn ($r) => $r->bill_length_mm > 50, between('flipper_length_mm', 190, 200))
    ->select('species', 'island', 'bill_length_mm', 'flipper_length_mm');

// filter_out() drops the rows where every condition is true.
echo "\n", phplyr($penguins)->filterOut(fn ($r) => $r->species === 'Adelie')->count();
```

:::

```{.text .output}
# A tibble: 14 × 4
   species   island bill_length_mm flipper_length_mm
   <chr>     <chr>           <dbl>             <int>
 1 Chinstrap Dream            51.3               193
 2 Chinstrap Dream            52.7               197
 3 Chinstrap Dream            51.3               197
 4 Chinstrap Dream            51.3               198
 5 Chinstrap Dream            51.7               194
 6 Chinstrap Dream            50.3               197
 7 Chinstrap Dream            50.6               193
 8 Chinstrap Dream            52                 197
 9 Chinstrap Dream            50.5               200
10 Chinstrap Dream            50.9               196
11 Chinstrap Dream            50.9               196
12 Chinstrap Dream            50.1               190
13 Chinstrap Dream            52.2               197
14 Chinstrap Dream            50.2               198
# A tibble: 1 × 1
      n
  <int>
1   192
```
