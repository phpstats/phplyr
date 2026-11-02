::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
// Load the penguins into an in-memory SQLite database, with typed columns.
$pdo = new PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE penguins (species TEXT, island TEXT, bill_length_mm REAL, bill_depth_mm REAL,
    flipper_length_mm INTEGER, body_mass_g INTEGER, sex TEXT, year INTEGER)');
$insert = $pdo->prepare('INSERT INTO penguins VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ((DataFrame::fromCsv("$data/penguins.csv") |> select(except('rownames')))->toRows() as $row) {
    $insert->execute(array_values($row));
}

// The same functions build a query. Nothing runs until collect().
$query = tbl($pdo, 'penguins') |>
    filter(col('body_mass_g')->isNa()->not()) |>
    group_by('species', 'island') |>
    summarize(penguins: n(), avg_mass_g: mean('body_mass_g')) |>
    filter(col('penguins')->ge(50)) |>
    arrange(desc('avg_mass_g'));

echo $query |> show_query(), "\n\n";
echo $query |> collect();
```

## PHP 8.3–8.4

```php
// Load the penguins into an in-memory SQLite database, with typed columns.
$pdo = new PDO('sqlite::memory:');
$pdo->exec('CREATE TABLE penguins (species TEXT, island TEXT, bill_length_mm REAL, bill_depth_mm REAL,
    flipper_length_mm INTEGER, body_mass_g INTEGER, sex TEXT, year INTEGER)');
$insert = $pdo->prepare('INSERT INTO penguins VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach (phplyr(DataFrame::fromCsv("$data/penguins.csv"))->select(except('rownames'))->get()->toRows() as $row) {
    $insert->execute(array_values($row));
}

// The same functions build a query. Nothing runs until collect().
$query = phplyr(tbl($pdo, 'penguins'))
    ->filter(col('body_mass_g')->isNa()->not())
    ->groupBy('species', 'island')
    ->summarize(penguins: n(), avg_mass_g: mean('body_mass_g'))
    ->filter(col('penguins')->ge(50))
    ->arrange(desc('avg_mass_g'))
    ->get();

echo phplyr($query)->showQuery(), "\n\n";
echo phplyr($query)->collect();
```

:::

```{.text .output}
SELECT "species", "island", "penguins", "avg_mass_g" FROM (SELECT * FROM (SELECT "species", "island", "penguins", "avg_mass_g", ROW_NUMBER() OVER (ORDER BY ("species" IS NULL), "species", ("island" IS NULL), "island") AS "__phibble_row" FROM (SELECT "species" AS "species", "island" AS "island", COUNT(*) AS "penguins", (CASE WHEN COUNT(*) > COUNT("body_mass_g") THEN NULL ELSE (CASE WHEN COUNT("body_mass_g") = 0 THEN NULL ELSE AVG(CAST("body_mass_g" AS REAL)) END) END) AS "avg_mass_g" FROM (SELECT * FROM (SELECT *, rowid AS "__phibble_row" FROM "penguins") AS q1 WHERE (NOT ("body_mass_g" IS NULL))) AS q2 GROUP BY "species", "island") AS q3) AS q4 WHERE ("penguins" >= ?)) AS q5 ORDER BY ("avg_mass_g" IS NULL), "avg_mass_g" DESC, ("__phibble_row" IS NULL), "__phibble_row"

# A tibble: 4 × 4
# Groups:   species [3]
  species   island    penguins avg_mass_g
  <chr>     <chr>        <int>      <dbl>
1 Gentoo    Biscoe         123      5076.
2 Chinstrap Dream           68      3733.
3 Adelie    Torgersen       51      3706.
4 Adelie    Dream           56      3688.
```
