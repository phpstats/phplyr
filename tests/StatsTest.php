<?php

declare(strict_types=1);

use Phplyr\Stats;

it('computes descriptives like R', function (string $case, Closure $compute, bool $useExtension) {
    $expected = rReference('stats-reference.json')[$case];
    if ($useExtension && ! extension_loaded('phit')) {
        test()->markTestSkipped('phit is not loaded');
    }
    Stats::$useExtension = $useExtension;
    try {
        $actual = $compute();
    } finally {
        Stats::$useExtension = true;
    }
    if ($expected === null) {
        expect($actual)->toBeNull();
    } else {
        expectClose($actual, $expected, 1e-12, 1e-12);
    }
})->with(function () {
    $x = [2.0, 4.0, 4.0, 5.0, 7.0, 9.0];

    return [
        'mean(x)' => ['mean(x)', fn () => Stats::mean($x)],
        'var(x)' => ['var(x)', fn () => Stats::var($x)],
        'sd(x)' => ['sd(x)', fn () => Stats::sd($x)],
        'median(x)' => ['median(x)', fn () => Stats::median($x)],
        'quantile(x, 0.9)' => ['quantile(x, 0.9)', fn () => Stats::quantile($x, 0.9)],
        'quantile(x, 0)' => ['quantile(x, 0)', fn () => Stats::quantile($x, 0.0)],
        'quantile(x, 1)' => ['quantile(x, 1)', fn () => Stats::quantile($x, 1.0)],
        'sum(x)' => ['sum(x)', fn () => Stats::sum($x)],
        'min(x)' => ['min(x)', fn () => Stats::min($x)],
        'max(x)' => ['max(x)', fn () => Stats::max($x)],
        'mean(c(1, NA, 3))' => ['mean(c(1, NA, 3))', fn () => Stats::mean([1, null, 3])],
        'mean(c(1, NA, 3), na.rm = TRUE)' => ['mean(c(1, NA, 3), na.rm = TRUE)', fn () => Stats::mean([1, null, 3], naRm: true)],
        'sd(c(1, NaN, 3), na.rm = TRUE)' => ['sd(c(1, NaN, 3), na.rm = TRUE)', fn () => Stats::sd([1, NAN, 3], naRm: true)],
        'var(1)' => ['var(1)', fn () => Stats::var([1.0])],
    ];
})->with(['phit extension' => true, 'PHP' => false])->group('verify:descriptives');

it('handles missing values and small inputs', function () {
    bothBackends(function () {
        $x = [1, null, 3];
        expect(Stats::mean($x))->toBeNull();
        expect(Stats::mean($x, naRm: true))->toEqualWithDelta(2.0, 1e-12);
        expect(Stats::sd([1, NAN, 3], naRm: true))->toEqualWithDelta(sqrt(2), 1e-12);
        expect(Stats::var([1.0]))->toBeNull();
        expect(Stats::mean([]))->toBeNull();
        expect(Stats::sum([]))->toBe(0);
        expect(Stats::max([null], naRm: true))->toBeNull();
    });
});

it('rejects non-numeric input and bad probabilities', function () {
    expect(fn () => Stats::mean(['a', 'b']))->toThrow(InvalidArgumentException::class);
    expect(fn () => Stats::quantile([1, 2], 1.5))->toThrow(InvalidArgumentException::class);
});
