::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{filter, mutate, select, count};
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{pivot_longer, pivot_wider};
use function Phplyr\{case_when, if_else, col, row_number};
use function Phplyr\{n, mean, sd, lm, glm, lmer};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Clean: drop incomplete rows, band body mass,
// compute the bill ratio.
$mass = col('body_mass_g');
$clean = $penguins |>
    filter(
        $mass->isNa()->not(),
        col('sex')->isNa()->not(),
    ) |>
    mutate(
        mass_band: case_when(
            [$mass->lt(3500), 'light'],
            [$mass->lt(4500), 'mid'],
            default: 'heavy',
        ),
        bill_ratio: col('bill_length_mm')
            ->div(col('bill_depth_mm')),
        male: if_else(col('sex')->eq('male'), 1, 0),
    );

// Reshape: one row per penguin and bill measure.
$long = $clean |>
    mutate(penguin: row_number()) |>
    select(
        'penguin',
        'species',
        ['length' => 'bill_length_mm'],
        ['depth' => 'bill_depth_mm'],
    ) |>
    pivot_longer(
        ['length', 'depth'],
        namesTo: 'measure',
        valuesTo: 'mm',
    );

echo $long |>
    group_by('measure') |>
    summarize(mean: mean('mm'), sd: sd('mm')), "\n";

// Summarize: penguins per species and mass band, then
// the species by mean bill ratio.
echo $clean |>
    count('species', 'mass_band') |>
    pivot_wider(
        namesFrom: 'mass_band',
        valuesFrom: 'n',
        valuesFill: 0,
    ), "\n";

echo $clean |>
    group_by('species') |>
    summarize(n: n(), mean_ratio: mean('bill_ratio')) |>
    arrange(desc('mean_ratio')), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
echo $coef($clean |> lm(
    'body_mass_g ~ flipper_length_mm + male',
)), "\n";
echo $coef($clean |> glm(
    'male ~ body_mass_g + bill_depth_mm',
    family: 'binomial',
)), "\n";
echo $coef($clean |> lmer(
    'body_mass_g ~ flipper_length_mm + male'
    . ' + (1 | species)',
)), "\n";
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, case_when, if_else};
use function Phplyr\{col, row_number, n, mean, sd};

$data = 'https://raw.githubusercontent.com/phpstats'
    . '/datasets/main/csv';
$penguins = DataFrame::fromCsv("$data/penguins.csv");

// Clean: drop incomplete rows, band body mass,
// compute the bill ratio.
$mass = col('body_mass_g');
$clean = phplyr($penguins)
    ->filter(
        $mass->isNa()->not(),
        col('sex')->isNa()->not(),
    )
    ->mutate(
        mass_band: case_when(
            [$mass->lt(3500), 'light'],
            [$mass->lt(4500), 'mid'],
            default: 'heavy',
        ),
        bill_ratio: col('bill_length_mm')
            ->div(col('bill_depth_mm')),
        male: if_else(col('sex')->eq('male'), 1, 0),
    )
    ->get();

// Reshape: one row per penguin and bill measure.
$long = phplyr($clean)
    ->mutate(penguin: row_number())
    ->select(
        'penguin',
        'species',
        ['length' => 'bill_length_mm'],
        ['depth' => 'bill_depth_mm'],
    )
    ->pivotLonger(
        ['length', 'depth'],
        namesTo: 'measure',
        valuesTo: 'mm',
    );

echo $long
    ->groupBy('measure')
    ->summarize(mean: mean('mm'), sd: sd('mm')), "\n";

// Summarize: penguins per species and mass band, then
// the species by mean bill ratio.
echo phplyr($clean)
    ->count('species', 'mass_band')
    ->pivotWider(
        namesFrom: 'mass_band',
        valuesFrom: 'n',
        valuesFill: 0,
    ), "\n";

echo phplyr($clean)
    ->groupBy('species')
    ->summarize(n: n(), mean_ratio: mean('bill_ratio'))
    ->arrange(desc('mean_ratio')), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
echo $coef(phplyr($clean)->lm(
    'body_mass_g ~ flipper_length_mm + male',
)), "\n";
echo $coef(phplyr($clean)->glm(
    'male ~ body_mass_g + bill_depth_mm',
    family: 'binomial',
)), "\n";
echo $coef(phplyr($clean)->lmer(
    'body_mass_g ~ flipper_length_mm + male'
    . ' + (1 | species)',
)), "\n";
```

:::

```{.text .output}
# A tibble: 2 × 3
  measure  mean    sd
  <chr>   <dbl> <dbl>
1 depth    17.2  1.97
2 length   44.0  5.47
# A tibble: 3 × 4
  species   heavy light   mid
  <chr>     <int> <int> <int>
1 Adelie        8    51    87
2 Chinstrap     3    17    48
3 Gentoo      104     0    15
# A tibble: 3 × 3
  species       n mean_ratio
  <chr>     <int>      <dbl>
1 Gentoo      119       3.18
2 Chinstrap    68       2.65
3 Adelie      146       2.12
(Intercept) -5410.3002  flipper_length_mm 46.9822  male 347.8503
(Intercept) -56.7794  body_mass_g 0.0053  bill_depth_mm 2.0257
(Intercept) -202.2405  flipper_length_mm 20.4609  male 527.4247
```
