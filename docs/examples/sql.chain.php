<?php

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
