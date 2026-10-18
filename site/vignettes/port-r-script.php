<?php

// Vignette for phplyr.phpstats.org: an R analysis of the schools data,
// translated to phplyr and run live. The inputs change the cleaning step.

declare(strict_types=1);

use Phibble\DataFrame;

use function Phplyr\{case_when, col, desc, if_else, mean, n, phplyr, row_number, sd};

return [
    'inputs' => [
        'pass_mark' => ['type' => 'number', 'label' => 'Pass mark', 'min' => 60, 'max' => 90, 'step' => 1, 'default' => 75],
        'low' => ['type' => 'number', 'label' => 'Low band below (hours)', 'min' => 1, 'max' => 6, 'step' => 0.5, 'default' => 4],
        'high' => ['type' => 'number', 'label' => 'High band from (hours)', 'min' => 6, 'max' => 12, 'step' => 0.5, 'default' => 8],
    ],
    'run' => function (array $input, string $data): array {
        $passMark = (float) $input['pass_mark'];
        $low = (float) $input['low'];
        $high = \max((float) $input['high'], $low + 0.5);

        $schools = DataFrame::fromCsv("{$data}/schools.csv");

        $clean = phplyr($schools)
            ->filter(col('score')->isNa()->not(), col('study_hours')->gt(0))
            ->mutate(
                hours_band: case_when([col('study_hours')->lt($low), 'low'], [col('study_hours')->lt($high), 'mid'], default: 'high'),
                gain: col('score')->sub(col('prior_score')),
                passed: if_else(col('score')->ge($passMark), 1, 0),
            )
            ->get();

        $long = phplyr($clean)
            ->mutate(student: row_number())
            ->select('student', 'school', ['prior' => 'prior_score', 'final' => 'score'])
            ->pivot_longer(['prior', 'final'], names_to: 'test', values_to: 'points')
            ->get();

        $tests = phplyr($long)->group_by('test')->summarise(mean: mean('points'), sd: sd('points'))->get();

        $bands = phplyr($clean)
            ->count('school', 'hours_band')
            ->pivot_wider(names_from: 'hours_band', values_from: 'n', values_fill: 0)
            ->get();

        $gains = phplyr($clean)
            ->group_by('school')
            ->summarise(n: n(), mean_gain: mean('gain'), pass_rate: mean('passed'))
            ->arrange(desc('mean_gain'))
            ->get();

        $byBand = phplyr($clean)
            ->group_by('hours_band')
            ->summarise(students: n(), mean_gain: mean('gain'), pass_rate: mean('passed'))
            ->get();

        $r = <<<'R'
        clean <- schools |>
          filter(!is.na(score), study_hours > 0) |>
          mutate(
            hours_band = case_when(study_hours < 4 ~ "low", study_hours < 8 ~ "mid", .default = "high"),
            gain = score - prior_score,
            passed = as.integer(score >= 75)
          )

        clean |>
          group_by(school) |>
          summarise(n = n(), mean_gain = mean(gain), pass_rate = mean(passed)) |>
          arrange(desc(mean_gain))
        R;

        $chain = <<<'PHP'
        $clean = phplyr($schools)
            ->filter(col('score')->isNa()->not(), col('study_hours')->gt(0))
            ->mutate(
                hours_band: case_when([col('study_hours')->lt(4), 'low'], [col('study_hours')->lt(8), 'mid'], default: 'high'),
                gain: col('score')->sub(col('prior_score')),
                passed: if_else(col('score')->ge(75), 1, 0),
            )
            ->get();

        $gains = phplyr($clean)
            ->group_by('school')
            ->summarise(n: n(), mean_gain: mean('gain'), pass_rate: mean('passed'))
            ->arrange(desc('mean_gain'))
            ->get();
        PHP;

        $pipe = <<<'PHP'
        $clean = $schools
            |> filter(col('score')->isNa()->not(), col('study_hours')->gt(0))
            |> mutate(
                hours_band: case_when([col('study_hours')->lt(4), 'low'], [col('study_hours')->lt(8), 'mid'], default: 'high'),
                gain: col('score')->sub(col('prior_score')),
                passed: if_else(col('score')->ge(75), 1, 0),
            );

        $gains = $clean
            |> group_by('school')
            |> summarise(n: n(), mean_gain: mean('gain'), pass_rate: mean('passed'))
            |> arrange(desc('mean_gain'));
        PHP;

        $blocks = [
            ['type' => 'text', 'content' => 'This analysis of 250 students in 12 schools was written in R with dplyr and tidyr. '
                .'phplyr keeps the function and argument names, so the translation is mostly syntax: '
                .'bare column names become strings or `col()`, `=` becomes `:`, and `.default` becomes `default`. '
                .'The inputs change the pass mark and the study-time bands; the code below shows the defaults.'],
            ['type' => 'text', 'content' => "The R original:\n\n```r\n{$r}\n```"],
            ['type' => 'code', 'chain' => $chain, 'pipe' => $pipe],
            ['type' => 'metrics', 'items' => [
                ['label' => 'Students', 'value' => $clean->nrow(), 'format' => '%d'],
                ['label' => 'Pass rate', 'value' => 100 * array_sum($clean->column('passed')) / \max(1, $clean->nrow()), 'format' => '%.1f%%'],
                ['label' => 'Mean gain', 'value' => array_sum($clean->column('gain')) / \max(1, $clean->nrow()), 'format' => '%.2f'],
            ]],
            ['type' => 'table', 'title' => 'Schools by mean gain', 'data' => $gains, 'digits' => 3],
            ['type' => 'chart', 'title' => 'Mean gain and pass rate by study-time band', 'plot' => [
                '$schema' => 'https://vega.github.io/schema/vega-lite/v5.json',
                'width' => 'container',
                'height' => 260,
                'data' => ['values' => $byBand->toRows()],
                'mark' => ['type' => 'bar'],
                'encoding' => [
                    'x' => ['field' => 'hours_band', 'type' => 'nominal', 'sort' => ['low', 'mid', 'high'], 'title' => 'Study-time band'],
                    'y' => ['field' => 'mean_gain', 'type' => 'quantitative', 'title' => 'Mean gain (points)'],
                    'tooltip' => [
                        ['field' => 'students', 'type' => 'quantitative'],
                        ['field' => 'mean_gain', 'type' => 'quantitative', 'format' => '.2f'],
                        ['field' => 'pass_rate', 'type' => 'quantitative', 'format' => '.0%'],
                    ],
                ],
            ]],
            ['type' => 'text', 'content' => 'Reshaping works the same way. `pivot_longer()` gives one row per student and test, and `pivot_wider()` spreads the band counts into columns.'],
            ['type' => 'table', 'title' => 'Prior and final scores', 'data' => $tests, 'digits' => 3],
            ['type' => 'table', 'title' => 'Students per school and band', 'data' => $bands],
        ];

        if (\function_exists('Phit\\fit_lm')) {
            $blocks[] = ['type' => 'text', 'content' => 'With the phit extension loaded, `lm()` and `glm()` fit models to the table, as R\'s functions do.'];
            $blocks[] = ['type' => 'code', 'chain' => "\$fit = phplyr(\$clean)->lm('score ~ study_hours + prior_score');\n\$logit = phplyr(\$clean)->glm('passed ~ study_hours + prior_score', family: 'binomial');", 'pipe' => "\$fit = \$clean |> lm('score ~ study_hours + prior_score');\n\$logit = \$clean |> glm('passed ~ study_hours + prior_score', family: 'binomial');"];
            $blocks[] = ['type' => 'model', 'title' => 'Linear model of the final score', 'result' => phplyr($clean)->lm('score ~ study_hours + prior_score')];
            $passed = array_sum($clean->column('passed'));
            if ($passed > 0 && $passed < $clean->nrow()) {
                $blocks[] = ['type' => 'model', 'title' => "Logistic model of passing (score ≥ {$passMark})", 'result' => phplyr($clean)->glm('passed ~ study_hours + prior_score', family: 'binomial')];
            } else {
                $blocks[] = ['type' => 'note', 'content' => 'Every student is on the same side of this pass mark, so there is no logistic model to fit.'];
            }
        }

        return $blocks;
    },
];
