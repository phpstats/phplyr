<?php

// Vignette for phplyr.phpstats.org: tidyr reshaping of the schools data,
// from a long summary to a wide table and back.

declare(strict_types=1);

use Phibble\DataFrame;

use function Phplyr\{case_when, col, mean, n, phplyr};

return [
    'inputs' => [
        'measure' => ['type' => 'radio', 'label' => 'Value in each cell', 'options' => ['students' => 'Students', 'mean_score' => 'Mean final score', 'mean_gain' => 'Mean gain'], 'default' => 'mean_score'],
        'fill' => ['type' => 'checkbox', 'label' => 'Fill empty cells with 0', 'default' => false],
        'prefix' => ['type' => 'checkbox', 'label' => 'Prefix the new columns with "band_"', 'default' => true],
    ],
    'run' => function (array $input, string $data): array {
        $measure = \in_array($input['measure'], ['students', 'mean_score', 'mean_gain'], true) ? $input['measure'] : 'mean_score';
        $fill = (bool) $input['fill'];
        $prefix = (bool) $input['prefix'] ? 'band_' : '';

        $schools = DataFrame::fromCsv("{$data}/schools.csv");

        // Long: one row per school and study-time band.
        $long = phplyr($schools)
            ->mutate(
                band: case_when([col('study_hours')->lt(4), 'low'], [col('study_hours')->lt(8), 'mid'], default: 'high'),
                gain: col('score')->sub(col('prior_score')),
            )
            ->group_by('school', 'band')
            ->summarise(students: n(), mean_score: mean('score', na_rm: true), mean_gain: mean('gain', na_rm: true))
            ->get();

        // Wide: one row per school, one column per band.
        $wide = phplyr($long)
            ->pivot_wider(id_cols: 'school', names_from: 'band', values_from: $measure, names_prefix: $prefix, values_fill: $fill ? 0 : null)
            ->separate_wider_delim('school', '-', names: ['kind', 'number'], cols_remove: false)
            ->get();

        // Back to long: pivot_longer() undoes pivot_wider().
        $back = phplyr($wide)
            ->select('school', $prefix.'low', $prefix.'mid', $prefix.'high')
            ->pivot_longer([$prefix.'low', $prefix.'mid', $prefix.'high'], names_to: 'band', names_prefix: $prefix === '' ? null : $prefix, values_to: $measure)
            ->get();

        $chain = <<<'PHP'
        $long = phplyr($schools)
            ->mutate(band: case_when([col('study_hours')->lt(4), 'low'], [col('study_hours')->lt(8), 'mid'], default: 'high'))
            ->group_by('school', 'band')
            ->summarise(students: n(), mean_score: mean('score', na_rm: true))
            ->get();

        $wide = phplyr($long)
            ->pivot_wider(id_cols: 'school', names_from: 'band', values_from: 'mean_score', names_prefix: 'band_')
            ->separate_wider_delim('school', '-', names: ['kind', 'number'], cols_remove: false)
            ->get();

        $back = phplyr($wide)
            ->pivot_longer(starts_with('band_'), names_to: 'band', names_prefix: 'band_', values_to: 'mean_score')
            ->get();
        PHP;
        $pipe = <<<'PHP'
        $long = $schools
            |> mutate(band: case_when([col('study_hours')->lt(4), 'low'], [col('study_hours')->lt(8), 'mid'], default: 'high'))
            |> group_by('school', 'band')
            |> summarise(students: n(), mean_score: mean('score', na_rm: true));

        $wide = $long
            |> pivot_wider(id_cols: 'school', names_from: 'band', values_from: 'mean_score', names_prefix: 'band_')
            |> separate_wider_delim('school', '-', names: ['kind', 'number'], cols_remove: false);

        $back = $wide
            |> pivot_longer(starts_with('band_'), names_to: 'band', names_prefix: 'band_', values_to: 'mean_score');
        PHP;

        $label = ['students' => 'Students', 'mean_score' => 'Mean final score', 'mean_gain' => 'Mean gain'][$measure];
        $missing = 0;
        foreach (['low', 'mid', 'high'] as $b) {
            $missing += \count(array_filter($wide->column($prefix.$b), fn ($v) => $v === null));
        }

        return [
            ['type' => 'text', 'content' => 'tidyr\'s reshaping functions move data between long and wide layouts. '
                .'Here the schools data is summarised to one row per school and study-time band, '
                .'`pivot_wider()` turns the bands into columns, `separate_wider_delim()` splits the school id, '
                .'and `pivot_longer()` turns the band columns back into rows.'],
            ['type' => 'code', 'chain' => $chain, 'pipe' => $pipe],
            ['type' => 'table', 'title' => 'One row per school and band', 'data' => $long, 'digits' => 2],
            ['type' => 'table', 'title' => "{$label}, one column per band", 'data' => $wide, 'digits' => 2],
            $missing > 0
                ? ['type' => 'note', 'content' => "{$missing} school and band combinations have no students, so their cells are NA. `values_fill: 0` fills them."]
                : ['type' => 'note', 'content' => 'Every school and band combination has a value.'],
            ['type' => 'chart', 'title' => "{$label} by school and band", 'plot' => [
                '$schema' => 'https://vega.github.io/schema/vega-lite/v5.json',
                'width' => 'container',
                'height' => 300,
                'data' => ['values' => array_values(array_filter($back->toRows(), fn ($r) => $r[$measure] !== null))],
                'mark' => ['type' => 'bar'],
                'encoding' => [
                    'x' => ['field' => 'school', 'type' => 'nominal', 'title' => null],
                    'xOffset' => ['field' => 'band', 'sort' => ['low', 'mid', 'high']],
                    'y' => ['field' => $measure, 'type' => 'quantitative', 'title' => $label],
                    'color' => ['field' => 'band', 'type' => 'nominal', 'sort' => ['low', 'mid', 'high'], 'title' => 'Study time'],
                ],
            ]],
            ['type' => 'table', 'title' => 'Back to long with pivot_longer()', 'data' => $back, 'digits' => 2],
        ];
    },
];
