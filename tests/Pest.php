<?php

declare(strict_types=1);

use Phplyr\Stats;

function fixturePath(string $name): string
{
    return __DIR__.'/Fixtures/'.$name;
}

/** Run a test body once with phit (when loaded) and once in pure PHP. */
function bothBackends(Closure $body): void
{
    $backends = extension_loaded('phit') ? [true, false] : [false];
    try {
        foreach ($backends as $useExtension) {
            Stats::$useExtension = $useExtension;
            $body($useExtension);
        }
    } finally {
        Stats::$useExtension = true;
    }
}

/**
 * Records which tests check phplyr against a reference (R's output, or
 * DataFrame for other backends), and the floating-point differences they
 * allow. With PHPLYR_VERIFY_LOG set, each such test appends one JSON line to
 * that file; bin/site-verifications turns the log into site/verifications/.
 */
final class Verification
{
    /** @var array<string, true> */
    public static array $references = [];

    public static int $numeric = 0;

    public static float $maxAbs = 0.0;

    public static float $maxRel = 0.0;

    /** @var array<string, true> */
    public static array $kinds = [];

    public static function reset(): void
    {
        self::$references = [];
        self::$numeric = 0;
        self::$maxAbs = 0.0;
        self::$maxRel = 0.0;
        self::$kinds = [];
    }
}

/** Mark the running test as checked against $reference. */
function verifyAgainst(string $reference): void
{
    Verification::$references[$reference] = true;
}

/** Record a floating-point comparison of the running test. */
function recordDifference(int|float $actual, int|float $expected, string $kind): void
{
    $abs = abs($actual - $expected);
    Verification::$numeric++;
    Verification::$maxAbs = \max(Verification::$maxAbs, $abs);
    Verification::$maxRel = \max(Verification::$maxRel, $expected == 0 ? $abs : $abs / abs($expected));
    Verification::$kinds[$kind] = true;
}

/** An R reference fixture from tests/Fixtures; marks the test as checked against R. */
function rReference(string $file): mixed
{
    static $cache = [];
    verifyAgainst($file);

    return $cache[$file] ??= json_decode(file_get_contents(fixturePath($file)), true);
}

/** expect($actual) within $delta of $expected, recorded for the verification report. */
function expectClose(int|float $actual, int|float $expected, float $delta, string $kind = 'values'): void
{
    recordDifference($actual, $expected, $kind);
    expect($actual)->toEqualWithDelta($expected, $delta);
}

uses()->afterEach(function () {
    $log = getenv('PHPLYR_VERIFY_LOG');
    if ($log !== false && $log !== '' && Verification::$references !== []) {
        file_put_contents($log, json_encode([
            'file' => basename(Pest\TestSuite::getInstance()->getFilename()),
            'test' => $this->getPrintableTestCaseMethodName(),
            'data' => $this->dataSetAsString(),
            'references' => array_keys(Verification::$references),
            'assertions' => PHPUnit\Framework\Assert::getCount(),
            'numeric' => Verification::$numeric,
            'max_abs_error' => Verification::$maxAbs,
            'max_rel_error' => Verification::$maxRel,
            'kinds' => array_keys(Verification::$kinds),
        ])."\n", FILE_APPEND);
    }
    Verification::reset();
})->in(__DIR__);

function reference(string $key): mixed
{
    $json = rReference('dplyr-reference.json');

    return $json[$key];
}

function expectRowsClose(array $actual, array $expected): void
{
    expect(\count($actual))->toBe(\count($expected));
    foreach ($expected as $i => $row) {
        expect(array_keys($actual[$i]))->toBe(array_keys($row));
        foreach ($row as $col => $value) {
            if ($value !== null && $actual[$i][$col] !== null && (is_float($value) || is_float($actual[$i][$col]))) {
                expectClose($actual[$i][$col], $value, 1e-9 * \max(1.0, abs($value)), 'table values');
            } else {
                expect($actual[$i][$col])->toBe($value);
            }
        }
    }
}

/** Column names of an R reference (a length-1 vector arrives unboxed). */
function referenceNames(string $key): array
{
    return (array) reference($key);
}

/** Single-column R references as a flat list. */
function referenceColumn(string $key, string $column): array
{
    return array_column(reference($key), $column);
}

/** True when the installed phibble orders groups like dplyr (sorted keys). */
function phibbleSortsGroups(): bool
{
    $t = Phibble\DataFrame::fromRows([['g' => 'b'], ['g' => 'a']])->groupBy(['g']);

    return $t->summarise([])->column('g') === ['a', 'b'];
}

function panel(): Phibble\DataFrame
{
    return Phibble\DataFrame::fromCsv(fixturePath('panel.csv'));
}
