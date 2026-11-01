<?php

/*
 * lm(), glm(), lmer(), and glmer() on the phit extension and on
 * phit-polyfill. Each run is a fresh process: one warm-up per case and
 * implementation, then RUNS alternating rounds. A run generates its data, then
 * times one fit. Memory is the process's peak, from peakmem (phpolars'
 * bench/csv-pipeline/peakmem.c, on the PATH) on Linux and /usr/bin/time -l on
 * macOS. The polyfill's coefficients must match the extension's to 1e-6,
 * relative.
 *
 * Usage:
 *   PHIT_EXTENSION=/path/libphit.so PHIT_POLYFILL=/path/phit-polyfill \
 *     php bench/models.php [--runs=7] [--out=docs/benchmarks/models-results.json]
 *
 * PHIT_POLYFILL is a phit-polyfill checkout with its Composer dependencies
 * installed. The polyfill runs with the JIT disabled, which it requires.
 */

declare(strict_types=1);

use Phibble\DataFrame;

use function Phplyr\{glm, glmer, lm, lmer};

$cases = [
    'lm' => ['Linear regression, 100,000 rows, 10 predictors', 100_000, 0],
    'glm' => ['Logistic regression, 100,000 rows, 10 predictors', 100_000, 0],
    'lmer' => ['Linear mixed model, 10,000 rows, 100 random intercepts', 10_000, 100],
    'glmer' => ['Logistic mixed model, 10,000 rows, 100 random intercepts', 10_000, 100],
];

$options = getopt('', ['child:', 'polyfill:', 'runs:', 'out:']);

if (isset($options['child'])) {
    // One fit: generate the data, time the fit, print the result.
    require dirname(__DIR__).'/vendor/autoload.php';
    if (isset($options['polyfill'])) {
        require $options['polyfill'].'/vendor/autoload.php';
    }
    [, $n, $groups] = $cases[$options['child']];
    mt_srand(5);
    $normal = fn () => sqrt(-2 * log((mt_rand() + 1) / (mt_getrandmax() + 2))) * cos(2 * M_PI * mt_rand() / mt_getrandmax());
    $p = $groups > 0 ? 2 : 10;
    $columns = [];
    $effects = array_map(fn () => $normal(), range(1, max($groups, 1)));
    for ($i = 0; $i < $n; $i++) {
        $eta = 0.5;
        for ($j = 1; $j <= $p; $j++) {
            $x = $normal();
            $columns["x{$j}"][] = $x;
            $eta += $x / $j;
        }
        if ($groups > 0) {
            $g = $i % $groups;
            $columns['g'][] = sprintf('g%03d', $g);
            $eta += $effects[$g];
        }
        $columns['y'][] = $eta + $normal();
        $columns['passed'][] = mt_rand() / mt_getrandmax() < 1 / (1 + exp(-$eta)) ? 1 : 0;
    }
    $data = DataFrame::fromColumns($columns);
    $rhs = implode(' + ', array_map(fn ($j) => "x{$j}", range(1, $p)));
    $fit = match ($options['child']) {
        'lm' => fn () => $data |> lm("y ~ {$rhs}"),
        'glm' => fn () => $data |> glm("passed ~ {$rhs}", family: 'binomial'),
        'lmer' => fn () => $data |> lmer("y ~ {$rhs} + (1 | g)"),
        'glmer' => fn () => $data |> glmer("passed ~ {$rhs} + (1 | g)", family: 'binomial'),
    };
    $start = hrtime(true);
    $result = $fit();
    $seconds = (hrtime(true) - $start) / 1e9;
    echo json_encode(['seconds' => $seconds, 'coefficients' => $result['coefficients']]);
    exit(0);
}

$extension = getenv('PHIT_EXTENSION') ?: throw new RuntimeException('Set PHIT_EXTENSION to the phit extension.');
$polyfill = getenv('PHIT_POLYFILL') ?: throw new RuntimeException('Set PHIT_POLYFILL to a phit-polyfill checkout.');
$runs = (int) ($options['runs'] ?? 7);
$out = $options['out'] ?? null;
$darwin = PHP_OS_FAMILY === 'Darwin';
$implementations = [
    'phit extension' => [PHP_BINARY, '-dmemory_limit=-1', '-dextension='.$extension, __FILE__],
    'phit-polyfill' => [PHP_BINARY, '-dmemory_limit=-1', '-dopcache.jit=disable', __FILE__, '--polyfill='.$polyfill],
];

$once = function (string $case, array $cmd) use ($darwin): array {
    $proc = proc_open([$darwin ? '/usr/bin/time' : 'peakmem', '-l', ...$cmd, '--child='.$case], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    if (proc_close($proc) !== 0) {
        throw new RuntimeException("{$case}: {$stderr}");
    }
    $run = json_decode($stdout, true, flags: JSON_THROW_ON_ERROR);
    $run['peak_memory_mib'] = preg_match('/(\d+)\s+peak memory footprint/', $stderr, $m) ? round($m[1] / 1024 ** 2, 1) : null;

    return $run;
};

$git = fn (string $dir) => trim((string) shell_exec('git -C '.escapeshellarg($dir).' rev-parse --short HEAD 2>/dev/null')) ?: null;
$median = function (array $xs): float {
    sort($xs);
    $m = intdiv(\count($xs), 2);

    return \count($xs) % 2 ? $xs[$m] : ($xs[$m - 1] + $xs[$m]) / 2;
};

$all = [];
$last = [];
foreach ($cases as $case => $_) {
    foreach ($implementations as $name => $cmd) {
        $last[$case][$name] = $once($case, $cmd);
    }
}
for ($i = 0; $i < $runs; $i++) {
    foreach ($cases as $case => $_) {
        foreach ($implementations as $name => $cmd) {
            $all[$case][$name][] = $last[$case][$name] = $once($case, $cmd);
        }
    }
}

$results = [];
foreach ($cases as $case => [$title, $n, $groups]) {
    $reference = $last[$case]['phit extension']['coefficients'];
    foreach ($implementations as $name => $_) {
        $worst = 0.0;
        foreach ($last[$case][$name]['coefficients'] as $k => $b) {
            $worst = max($worst, abs($b - $reference[$k]) / max(1.0, abs($reference[$k])));
        }
        if ($worst > 1e-6) {
            throw new RuntimeException("{$case}: {$name}'s coefficients differ from the extension's by {$worst}");
        }
        $seconds = array_column($all[$case][$name], 'seconds');
        $memory = array_column($all[$case][$name], 'peak_memory_mib');
        $results[] = [
            'case' => $case,
            'title' => $title,
            'rows' => $n,
            'implementation' => $name,
            'median_s' => round($median($seconds), 5),
            'runs_s' => array_map(fn ($s) => round($s, 5), $seconds),
            'median_peak_memory_mib' => $memory === [] || \in_array(null, $memory, true) ? null : $median($memory),
            'runs_peak_memory_mib' => $memory,
            'max_coefficient_difference' => $worst,
        ];
        fprintf(STDERR, "%-6s %-15s %9.4f s\n", $case, $name, $median($seconds));
    }
}

$record = [
    'schema' => 'phplyr-models/1',
    'generated_at' => gmdate('Y-m-d\TH:i:s\Z'),
    'method' => [
        'statistic' => 'median',
        'runs' => $runs,
        'timing' => 'one fit per fresh process, after the data is generated; one warm-up run per case and implementation is not recorded',
        'memory' => $darwin ? 'peak memory footprint (/usr/bin/time -l)' : 'peak anonymous memory (peakmem)',
        'polyfill_settings' => 'opcache.jit=disable',
        'machine' => (require __DIR__.'/machine.php')(),
        'versions' => [
            'phplyr' => $git(dirname(__DIR__)),
            'phit_extension' => $git(dirname($extension, 3)),
            'phit_polyfill' => $git($polyfill),
        ],
    ],
    'results' => $results,
];
if ($out !== null) {
    file_put_contents($out, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
}
echo json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
