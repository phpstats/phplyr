::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo implode(', ', ($penguins |> select('species', ends_with('_mm')))->columns()), "\n";
echo implode(', ', ($penguins |> select(except('rownames')))->columns()), "\n";
echo implode(', ', ($penguins |> select(where('numeric')))->columns()), "\n";
echo implode(', ', ($penguins |> select(['mass' => 'body_mass_g'], 'species'))->columns()), "\n";
echo implode(', ', ($penguins |> relocate('sex', _before: 'island'))->columns()), "\n";
echo implode(', ', ($penguins |> rename_with(strtoupper(...), contains('length')))->columns()), "\n";
```

## PHP 8.3–8.4

```php
$penguins = DataFrame::fromCsv("$data/penguins.csv");

echo implode(', ', (phplyr($penguins)->select('species', ends_with('_mm'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(except('rownames'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(where('numeric'))->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->select(['mass' => 'body_mass_g'], 'species')->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->relocate('sex', _before: 'island')->get())->columns()), "\n";
echo implode(', ', (phplyr($penguins)->renameWith(strtoupper(...), contains('length'))->get())->columns()), "\n";
```

:::

```{.text .output}
species, bill_length_mm, bill_depth_mm, flipper_length_mm
species, island, bill_length_mm, bill_depth_mm, flipper_length_mm, body_mass_g, sex, year
rownames, bill_length_mm, bill_depth_mm, flipper_length_mm, body_mass_g, year
mass, species
rownames, species, sex, island, bill_length_mm, bill_depth_mm, flipper_length_mm, body_mass_g, year
rownames, species, island, BILL_LENGTH_MM, bill_depth_mm, FLIPPER_LENGTH_MM, body_mass_g, sex, year
```
