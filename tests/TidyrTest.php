<?php

declare(strict_types=1);

use Phibble\DataFrame;

use function Phplyr\{arrange, complete, crossing, desc, drop_na, everything, except, expand, fill, filter, group_by, head, nest, nesting, pipe, pivot_longer, pivot_wider, replace_na, select, separate, separate_longer_delim, separate_longer_position, separate_wider_delim, separate_wider_position, separate_wider_regex, starts_with, uncount, ungroup, unite, unnest};

function tidyFixture(string $name): DataFrame
{
    return DataFrame::fromCsv(fixturePath($name));
}

function tidyrReference(string $key): array
{
    $json = rReference('tidyr-reference.json');

    return $json[$key];
}

dataset('tidyr', function () {
    $wide = fn () => tidyFixture('wide.csv');
    $long = fn () => tidyFixture('long.csv');
    $noZ = fn () => pipe($long(), filter(fn ($r) => $r->key !== 'z'));
    $strings = fn () => tidyFixture('strings.csv');
    $cases = [
        'longer' => fn () => pipe($wide(), pivot_longer(starts_with('wk'), namesTo: 'week', valuesTo: 'rank')),
        'longer_drop' => fn () => pipe($wide(), pivot_longer(['wk1', 'wk2', 'wk3'], namesTo: 'week', namesPrefix: 'wk', valuesDropNa: true)),
        'longer_sep' => fn () => pipe(tidyFixture('who.csv'), pivot_longer(except('country'), namesTo: ['diag', 'sex_age'], namesPrefix: 'new_', namesSep: '_')),
        'longer_pattern' => fn () => pipe(tidyFixture('who.csv'), pivot_longer(except('country'), namesTo: ['diag', 'sex', 'age'], namesPattern: 'new_(.*)_(.)(.*)')),
        'longer_value' => fn () => pipe(tidyFixture('family.csv'), pivot_longer(except('family'), namesTo: ['.value', 'child'], namesSep: '_', valuesDropNa: true)),
        'wider' => fn () => pipe($noZ(), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val')),
        'wider_fill' => fn () => pipe($noZ(), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val', valuesFill: 0, namesPrefix: 'k_')),
        'wider_fn' => fn () => pipe($long(), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val', valuesFn: fn ($v) => array_sum($v))),
        'wider_multi' => fn () => pipe($noZ(), pivot_wider(namesFrom: 'key', valuesFrom: ['val', 'w'])),
        'wider_sort' => fn () => pipe($noZ(), arrange(desc('key')), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val', namesSort: true)),
        'wider_glue' => fn () => pipe($noZ(), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val', namesGlue: '{key}_{.value}')),
        'complete' => fn () => pipe($long(), select('id', 'key', 'val'), filter(fn ($r) => $r->key !== 'z' || $r->val == 5), complete('id', 'key')),
        'complete_fill' => fn () => pipe($long(), select('id', 'key', 'w'), filter(fn ($r) => $r->key !== 'z' || $r->w === 50), complete('id', 'key', fill: ['w' => 0])),
        'complete_nesting' => fn () => pipe(panel(), filter(fn ($r) => $r->id <= 6), select('g', 'label', 'year'), complete(nesting('g', 'label'), year: [2001, 2002]), head(8)),
        'complete_grouped' => fn () => pipe($long(), select('id', 'key', 'w'), filter(fn ($r) => $r->key !== 'z' || $r->w === 50), group_by('id'), complete(key: ['x', 'y']), ungroup()),
        'expand' => fn () => pipe($long(), expand('id', 'key')),
        'expand_nesting' => fn () => pipe($long(), expand(nesting('id', 'key'))),
        'crossing' => fn () => crossing(x: [2, 1, 2], y: ['b', 'a']),
        'fill_down' => fn () => pipe($wide(), fill('wk1', 'wk2')),
        'fill_up' => fn () => pipe($wide(), fill('wk1', 'wk2', direction: 'up')),
        'fill_grouped' => fn () => pipe(panel(), select('id', 'g', 'value'), group_by('g'), fill('value', direction: 'downup'), ungroup()),
        'drop_na_all' => fn () => pipe($wide(), drop_na()),
        'drop_na_col' => fn () => pipe($wide(), drop_na('wk1')),
        'replace_na' => fn () => pipe($wide(), replace_na(['wk1' => 0, 'wk2' => -1])),
        'uncount' => fn () => pipe($strings(), select('id', 'n'), uncount('n', _id: 'copy')),
        'unite' => fn () => pipe(panel(), head(3), unite('gl', 'g', 'label', sep: ':')),
        'unite_keep' => fn () => pipe(panel(), head(3), unite('gv', 'g', 'value', remove: false)),
        'separate' => fn () => pipe($strings(), select('id', 's'), separate('s', ['a', 'b'], sep: '-', extra: 'drop', fill: 'right')),
        'separate_merge' => fn () => pipe($strings(), select('id', 's'), separate('s', ['a', 'b'], sep: '-', extra: 'merge', fill: 'left')),
        'separate_pos' => fn () => pipe($strings(), select('id', 'code'), separate('code', ['letters', 'digits'], sep: 2, convert: true)),
        'wider_delim' => fn () => pipe($strings(), select('id', 's'), separate_wider_delim('s', '-', names: ['p1', 'p2', 'p3'], tooFew: 'align_start')),
        'wider_delim_sep' => fn () => pipe($strings(), select('id', 's'), separate_wider_delim('s', '-', namesSep: '_', tooFew: 'align_end')),
        'wider_position' => fn () => pipe($strings(), select('id', 'code'), separate_wider_position('code', ['state' => 2, 1, 'digits' => 2], tooFew: 'align_start')),
        'wider_regex' => fn () => pipe($strings(), select('id', 'code'), separate_wider_regex('code', ['letters' => '[A-Z]+', 'digits' => '\d+'])),
        'longer_delim' => fn () => pipe($strings(), select('id', 'tags'), separate_longer_delim('tags', ',')),
        'longer_position' => fn () => pipe($strings(), select('id', 'code'), separate_longer_position('code', 2)),
    ];

    return array_combine(array_keys($cases), array_map(fn ($k, $c) => [$k, $c], array_keys($cases), $cases));
});

it('matches tidyr', function (string $key, Closure $pipeline) {
    $out = $pipeline();
    expectRowsClose(($out instanceof DataFrame ? $out : $out->collect())->toRows(), tidyrReference($key));
})->with('tidyr');

it('errors on ambiguous widening and ragged splits', function () {
    // Duplicate cells become list-columns with tidyr's warning.
    $warnings = [];
    set_error_handler(function (int $level, string $message) use (&$warnings) {
        $warnings[] = $message;

        return true;
    }, E_USER_WARNING);
    try {
        $wide = pipe(tidyFixture('long.csv'), pivot_wider(idCols: 'id', namesFrom: 'key', valuesFrom: 'val'));
    } finally {
        restore_error_handler();
    }
    expect($warnings[0] ?? '')->toStartWith('Values from `val` are not uniquely identified; output will contain list-cols.');
    expect($wide->toRows()[2]['z'])->toBe([5.0, 6.0]);
    expect($wide->toRows()[0]['x'])->toBe([1.5]);
    expect(fn () => pipe(tidyFixture('strings.csv'), separate_wider_delim('s', '-', names: ['a', 'b'])))
        ->toThrow(UnexpectedValueException::class, 'tooMany');
});

it('nests and unnests', function () {
    $nested = pipe(panel(), select('g', 'id', 'score'), group_by('g'), nest());
    expect($nested->nrow())->toBe(3);
    $back = pipe($nested, unnest('data'));
    expect($back->nrow())->toBe(12);
    $named = pipe(panel(), select('g', 'id', 'score'), nest(rows: ['id', 'score']));
    expect($named->columns())->toBe(['g', 'rows']);
});
