<?php

// Vignette for phplyr.phpstats.org: one pipeline on phibble's in-memory
// DataFrame, on SQLite, and on Polars, with timings and a check that every
// backend returns the same rows.

declare(strict_types=1);

use Phibble\DataFrame;
use Phibble\Polars\PolarsTable;
use Phibble\Sql\SqlTable;

use function Phplyr\{col, desc, mean, min_rank, n, phplyr, sum};

$polars = \extension_loaded('php_polars') && class_exists(PolarsTable::class);
$sqlite = \extension_loaded('pdo_sqlite');
$backends = ['dataframe' => 'DataFrame (in memory)'];
if ($sqlite) {
    $backends['sqlite'] = 'SQLite (SqlTable)';
}
if ($polars) {
    $backends['polars'] = 'Polars (PolarsTable)';
}

return [
    'inputs' => [
        'rows' => ['type' => 'select', 'label' => 'Sales rows', 'options' => [10000 => '10,000', 50000 => '50,000', 200000 => '200,000'], 'default' => 10000],
        'backend' => ['type' => 'radio', 'label' => 'Backend', 'options' => $backends, 'default' => 'dataframe'],
        'top' => ['type' => 'number', 'label' => 'Products per region', 'min' => 1, 'max' => 10, 'step' => 1, 'default' => 3],
    ],
    'run' => function (array $input, string $data) use ($backends): array {
        $rows = (int) $input['rows'];
        $backend = isset($backends[$input['backend']]) ? $input['backend'] : 'dataframe';
        $top = (int) $input['top'];

        // The same generated data on every run: 100 stores in 5 regions, 50 products.
        mt_srand(7);
        $sales = ['store' => [], 'product' => [], 'units' => [], 'price' => []];
        for ($i = 0; $i < $rows; $i++) {
            $sales['store'][] = mt_rand(1, 100);
            $sales['product'][] = 'p'.str_pad((string) mt_rand(1, 50), 2, '0', STR_PAD_LEFT);
            $r = mt_rand(1, 100);
            $sales['units'][] = $r <= 2 ? null : ($r <= 7 ? 0 : mt_rand(1, 20));
            $sales['price'][] = round(mt_rand(100, 10000) / 100, 2);
        }
        $stores = ['store' => range(1, 100), 'region' => array_map(fn ($s) => ['North', 'South', 'East', 'West', 'Central'][($s - 1) % 5], range(1, 100))];

        $tables = function (string $backend) use ($sales, $stores, $rows): array {
            $s = DataFrame::fromColumns($sales);
            $st = DataFrame::fromColumns($stores);
            if ($backend === 'polars') {
                return [PolarsTable::fromTable($s), PolarsTable::fromTable($st)];
            }
            if ($backend === 'sqlite') {
                $pdo = new PDO('sqlite::memory:');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec('CREATE TABLE sales (store INTEGER, product TEXT, units INTEGER, price REAL)');
                $pdo->exec('CREATE TABLE stores (store INTEGER, region TEXT)');
                $pdo->beginTransaction();
                $insert = $pdo->prepare('INSERT INTO sales VALUES (?, ?, ?, ?)');
                for ($i = 0; $i < $rows; $i++) {
                    $insert->execute([$sales['store'][$i], $sales['product'][$i], $sales['units'][$i], $sales['price'][$i]]);
                }
                $insert = $pdo->prepare('INSERT INTO stores VALUES (?, ?)');
                foreach ($stores['store'] as $i => $store) {
                    $insert->execute([$store, $stores['region'][$i]]);
                }
                $pdo->commit();

                return [SqlTable::table($pdo, 'sales'), SqlTable::table($pdo, 'stores')];
            }

            return [$s, $st];
        };

        $pipeline = fn ($sales, $stores) => phplyr($sales)
            ->filter(col('units')->gt(0))
            ->leftJoin($stores, by: 'store')
            ->groupBy('region', 'product')
            ->summarize(orders: n(), revenue: sum(col('units')->mul(col('price'))), avg_price: mean('price'))
            ->groupBy('region')
            ->mutate(rank: min_rank(desc('revenue')))
            ->filter(col('rank')->le($top))
            ->arrange('region', 'rank')
            ->collect()
            ->get();

        [$s, $st] = $tables($backend);
        $t = hrtime(true);
        $result = $pipeline($s, $st);
        $ms = (hrtime(true) - $t) / 1e6;

        // Check the rows against DataFrame, with floats rounded to 9 significant digits.
        $canonical = function (DataFrame $t): array {
            $lines = array_map(fn ($r) => json_encode(array_map(fn ($v) => \is_float($v) ? sprintf('%.9g', $v) : $v, $r)), $t->toRows());
            sort($lines);

            return $lines;
        };
        $matches = $backend === 'dataframe' ? true : $canonical($result) === $canonical($pipeline(...$tables('dataframe')));

        $chain = <<<'PHP'
        $top = phplyr($sales)
            ->filter(col('units')->gt(0))
            ->leftJoin($stores, by: 'store')
            ->groupBy('region', 'product')
            ->summarize(orders: n(), revenue: sum(col('units')->mul(col('price'))), avg_price: mean('price'))
            ->groupBy('region')
            ->mutate(rank: min_rank(desc('revenue')))
            ->filter(col('rank')->le(3))
            ->arrange('region', 'rank')
            ->collect()
            ->get();
        PHP;
        $pipe = <<<'PHP'
        $top = $sales |>
            filter(col('units')->gt(0)) |>
            left_join($stores, by: 'store') |>
            group_by('region', 'product') |>
            summarize(orders: n(), revenue: sum(col('units')->mul(col('price'))), avg_price: mean('price')) |>
            group_by('region') |>
            mutate(rank: min_rank(desc('revenue'))) |>
            filter(col('rank')->le(3)) |>
            arrange('region', 'rank') |>
            collect();
        PHP;

        $blocks = [
            ['type' => 'text', 'content' => 'phplyr functions call the methods of phibble\'s `Table` contract, so one pipeline runs on several backends. '
                .'`DataFrame` evaluates it in PHP. `SqlTable` compiles `col()` expressions and helpers such as `mean()` to SQL, and `collect()` runs the query. '
                .'`PolarsTable` runs the same expressions in Polars through the php_polars extension. '
                .'This pipeline joins sales to stores, sums revenue per region and product, and keeps each region\'s top products.'],
            ['type' => 'code', 'chain' => $chain, 'pipe' => $pipe],
            ['type' => 'metrics', 'items' => [
                ['label' => 'Pipeline time (ms)', 'value' => $ms, 'format' => '%.1f'],
                ['label' => 'Rows in', 'value' => $rows, 'format' => '%d'],
                ['label' => 'Rows out', 'value' => $result->nrow(), 'format' => '%d'],
            ]],
            match (true) {
                $backend === 'dataframe' => ['type' => 'note', 'content' => 'Choose another backend to check its rows against DataFrame.'],
                $matches => ['type' => 'note', 'content' => "{$backends[$backend]} returned the same rows as DataFrame."],
                default => ['type' => 'note', 'content' => "{$backends[$backend]} returned different rows from DataFrame."],
            },
            ['type' => 'chart', 'title' => 'Revenue of the top products in each region', 'plot' => [
                '$schema' => 'https://vega.github.io/schema/vega-lite/v5.json',
                'width' => 'container',
                'height' => 280,
                'data' => ['values' => $result->toRows()],
                'mark' => ['type' => 'bar'],
                'encoding' => [
                    'x' => ['field' => 'region', 'type' => 'nominal', 'title' => null],
                    'xOffset' => ['field' => 'rank', 'type' => 'ordinal'],
                    'y' => ['field' => 'revenue', 'type' => 'quantitative', 'title' => 'Revenue'],
                    'color' => ['field' => 'rank', 'type' => 'ordinal', 'title' => 'Rank'],
                    'tooltip' => [['field' => 'product'], ['field' => 'orders', 'type' => 'quantitative'], ['field' => 'revenue', 'type' => 'quantitative', 'format' => ',.0f']],
                ],
            ]],
            ['type' => 'table', 'title' => 'Result', 'data' => $result, 'digits' => 2],
        ];
        if ($backend === 'sqlite') {
            [$s, $st] = $tables('sqlite');
            $blocks[] = ['type' => 'text', 'content' => 'On SQLite, `show_query()` returns the SQL that phplyr generated.'];
            $blocks[] = ['type' => 'output', 'content' => phplyr($s)
                ->filter(col('units')->gt(0))
                ->leftJoin($st, by: 'store')
                ->groupBy('region', 'product')
                ->summarize(orders: n(), revenue: sum(col('units')->mul(col('price'))), avg_price: mean('price'))
                ->groupBy('region')
                ->mutate(rank: min_rank(desc('revenue')))
                ->filter(col('rank')->le($top))
                ->arrange('region', 'rank')
                ->showQuery()];
        }
        $blocks[] = ['type' => 'note', 'content' => 'The timing covers the pipeline only, on the site\'s server. Building the tables is not timed. The performance page has benchmarks at a million rows, with dplyr for comparison.'];

        return $blocks;
    },
];
