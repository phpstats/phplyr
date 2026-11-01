::: {.panel-tabset group="syntax"}

## PHP 8.5+

```php
use Phibble\DataFrame;
use function Phplyr\{filter, mutate, select, count, head};
use function Phplyr\{group_by, summarize, arrange, desc};
use function Phplyr\{pivot_longer, pivot_wider};
use function Phplyr\{case_when, if_else, col, row_number};
use function Phplyr\{n, mean, sd, lm, glm, lmer};

$schools = DataFrame::fromCsv('schools.csv');

// Clean: drop incomplete rows, band study time,
// compute the gain.
$hours = col('study_hours');
$clean = $schools |>
    filter(col('score')->isNa()->not(), $hours->gt(0)) |>
    mutate(
        hours_band: case_when(
            [$hours->lt(4), 'low'],
            [$hours->lt(8), 'mid'],
            default: 'high',
        ),
        gain: col('score')->sub(col('prior_score')),
        passed: if_else(col('score')->ge(75), 1, 0),
    );

// Reshape: one row per student and test.
$long = $clean |>
    mutate(student: row_number()) |>
    select(
        'student',
        'school',
        ['prior' => 'prior_score', 'final' => 'score'],
    ) |>
    pivot_longer(
        ['prior', 'final'],
        namesTo: 'test',
        valuesTo: 'points',
    );

echo $long |>
    group_by('test') |>
    summarize(
        mean: mean('points'),
        sd: sd('points'),
    ), "\n";

// Summarize: students per school and band, then
// the schools with the largest mean gains.
echo $clean |>
    count('school', 'hours_band') |>
    pivot_wider(
        namesFrom: 'hours_band',
        valuesFrom: 'n',
        valuesFill: 0,
    ) |>
    head(3), "\n";

echo $clean |>
    group_by('school') |>
    summarize(n: n(), mean_gain: mean('gain')) |>
    arrange(desc('mean_gain')) |>
    head(3), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
$rhs = 'study_hours + prior_score';
echo $coef($clean |> lm("score ~ {$rhs}")), "\n";
echo $coef($clean |> glm(
    "passed ~ {$rhs}",
    family: 'binomial',
)), "\n";
echo $coef($clean |> lmer(
    "score ~ {$rhs} + (1 | school)",
)), "\n";
```

## PHP 8.3–8.4

```php
use Phibble\DataFrame;
use function Phplyr\{phplyr, desc, case_when, if_else};
use function Phplyr\{col, row_number, n, mean, sd};

$schools = DataFrame::fromCsv('schools.csv');

// Clean: drop incomplete rows, band study time,
// compute the gain.
$hours = col('study_hours');
$clean = phplyr($schools)
    ->filter(col('score')->isNa()->not(), $hours->gt(0))
    ->mutate(
        hours_band: case_when(
            [$hours->lt(4), 'low'],
            [$hours->lt(8), 'mid'],
            default: 'high',
        ),
        gain: col('score')->sub(col('prior_score')),
        passed: if_else(col('score')->ge(75), 1, 0),
    )
    ->get();

// Reshape: one row per student and test.
$long = phplyr($clean)
    ->mutate(student: row_number())
    ->select(
        'student',
        'school',
        ['prior' => 'prior_score', 'final' => 'score'],
    )
    ->pivotLonger(
        ['prior', 'final'],
        namesTo: 'test',
        valuesTo: 'points',
    )
    ->get();

echo phplyr($long)
    ->groupBy('test')
    ->summarize(
        mean: mean('points'),
        sd: sd('points'),
    ), "\n";

// Summarize: students per school and band, then
// the schools with the largest mean gains.
echo phplyr($clean)
    ->count('school', 'hours_band')
    ->pivotWider(
        namesFrom: 'hours_band',
        valuesFrom: 'n',
        valuesFill: 0,
    )
    ->head(3), "\n";

echo phplyr($clean)
    ->groupBy('school')
    ->summarize(n: n(), mean_gain: mean('gain'))
    ->arrange(desc('mean_gain'))
    ->head(3), "\n";

// Model: linear, logistic, and random-intercept fits.
$coef = fn (array $fit) => implode('  ', array_map(
    fn ($name, $b) => $name.' '.\round($b, 4),
    $fit['columns'],
    $fit['coefficients'],
));
$rhs = 'study_hours + prior_score';
echo $coef(phplyr($clean)->lm("score ~ {$rhs}")), "\n";
echo $coef(phplyr($clean)->glm(
    "passed ~ {$rhs}",
    family: 'binomial',
)), "\n";
echo $coef(phplyr($clean)->lmer(
    "score ~ {$rhs} + (1 | school)",
)), "\n";
```

:::

```{.text .output}
# A tibble: 2 × 3
  test   mean    sd
  <chr> <dbl> <dbl>
1 final  72.0  10.3
2 prior  59.8  12.4
# A tibble: 3 × 4
  school     high   low   mid
  <chr>     <int> <int> <int>
1 school-01     7     4     7
2 school-02     8     8     8
3 school-03     5     3     7
# A tibble: 3 × 3
  school        n mean_gain
  <chr>     <int>     <dbl>
1 school-07    19      24.3
2 school-10    25      14.1
3 school-02    24      13.6
(Intercept) 45.3014  study_hours 1.5691  prior_score 0.273
(Intercept) -6.9865  study_hours 0.5688  prior_score 0.0395
(Intercept) 46.6627  study_hours 1.6141  prior_score 0.2453
```
