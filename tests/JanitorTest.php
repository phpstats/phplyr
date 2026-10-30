<?php

declare(strict_types=1);

use Phibble\DataFrame;
use Phibble\Type;

use function Phplyr\{clean_names, get_dupes, group_by, make_clean_names, pipe, remove_empty, tabyl};

function janitorRef(string $key): mixed
{
    return rReference('janitor-reference.json')[$key];
}

function emptyish(): DataFrame
{
    return DataFrame::fromColumns([
        'a' => [1.0, null, 2.0, null, 1.0],
        'b' => ['x', null, 'y', null, ''],
        'c' => [null, null, null, null, null],
        'd' => [null, null, 3.0, null, null],
    ], ['c' => Type::Bool]);
}

function dupesData(): DataFrame
{
    return DataFrame::fromColumns([
        'id' => [1.0, 2.0, 3.0, 4.0, 5.0, 6.0, 7.0],
        'g' => ['b', 'a', 'b', 'a', null, null, 'c'],
        'v' => [1.0, 1.0, 1.0, 2.0, 3.0, 3.0, 4.0],
    ]);
}

function tabData(): DataFrame
{
    return DataFrame::fromColumns([
        'g' => ['b', 'a', 'b', null, 'a', 'b', 'c', 'a'],
        'h' => ['x', 'y', 'x', 'x', null, 'y', '', 'x'],
        'k' => [3.0, 1.0, 3.0, 2.0, 1.0, 3.0, null, 2.0],
        'l' => [true, false, true, null, true, false, true, true],
        'f' => ['lo', 'hi', 'lo', 'hi', null, 'lo', 'lo', 'hi'],
    ])->asFactor('f', ['lo', 'mid', 'hi']);
}

describe('make_clean_names() and clean_names()', function () {
    it('matches janitor on names with case, punctuation, unicode, leading digits, and repeats', function () {
        $in = janitorRef('names_in');
        $expected = janitorRef('clean');

        expect(make_clean_names($in))->toBe($expected);
    })->skip(! class_exists(Transliterator::class), 'needs ext-intl for janitor-equal transliteration');

    it('renames a table\'s columns', function () {
        $t = DataFrame::fromColumns(['First Name' => ['a'], 'first_name' => ['b'], '% Done' => [1], '2020' => [2]]);

        expect(pipe($t, clean_names())->columns())->toBe(['first_name', 'first_name_2', 'percent_done', 'x2020']);
    });

    it('leaves clean names alone', function () {
        $t = DataFrame::fromColumns(['a' => [1], 'b_2' => [2]]);

        expect(pipe($t, clean_names()))->toBe($t);
    });
});

describe('remove_empty()', function () {
    it('matches janitor', function (array|string $which, float $cutoff, string $key) {
        expectRowsClose(pipe(emptyish(), remove_empty($which, $cutoff))->toRows(), janitorRef($key));
    })->with([
        'rows and cols' => [['rows', 'cols'], 1.0, 'remove_both'],
        'rows' => ['rows', 1.0, 'remove_rows'],
        'cols' => ['cols', 1.0, 'remove_cols'],
        'rows with a cutoff' => ['rows', 0.5, 'remove_rows_cutoff'],
        'cols with a cutoff' => ['cols', 0.3, 'remove_cols_cutoff'],
    ]);

    it('rejects a cutoff with both rows and cols', function () {
        remove_empty(['rows', 'cols'], 0.5);
    })->throws(InvalidArgumentException::class);
});

describe('get_dupes()', function () {
    it('matches janitor', function (array $columns, string $key) {
        $t = $key === 'dupes_all' ? dupesData()->select(['g', 'v']) : dupesData();

        expectRowsClose(pipe($t, get_dupes(...$columns))->toRows(), janitorRef($key));
    })->with([
        'all columns' => [[], 'dupes_all'],
        'one column' => [['g'], 'dupes_g'],
        'two columns' => [['g', 'v'], 'dupes_gv'],
        'a numeric column' => [['v'], 'dupes_v'],
    ]);

    it('keeps grouping without using it', function () {
        $out = pipe(dupesData(), group_by('g'), get_dupes('v'));

        expect($out->groups())->toBe(['g'])
            ->and($out->nrow())->toBe(5);
    });
});

describe('tabyl()', function () {
    it('matches janitor', function (string $var1, ?string $var2, bool $showNa, bool $showMissingLevels, string $key) {
        $t = $key === 'tabyl_h' ? tabData()->filter(fn ($r) => $r['h'] !== null) : tabData();
        $out = pipe($t, tabyl($var1, $var2, $showNa, $showMissingLevels));

        expectRowsClose($out->toRows(), janitorRef($key));
    })->with([
        'characters with NA' => ['g', null, true, true, 'tabyl_g'],
        'characters without NA' => ['g', null, false, true, 'tabyl_g_no_na'],
        'numbers' => ['k', null, true, true, 'tabyl_k'],
        'logicals' => ['l', null, true, true, 'tabyl_l'],
        'a factor with an unused level' => ['f', null, true, true, 'tabyl_f'],
        'a factor without unused levels' => ['f', null, true, false, 'tabyl_f_no_missing'],
        'an empty string' => ['h', null, true, true, 'tabyl_h'],
        'two ways' => ['g', 'h', true, true, 'tabyl_gh'],
        'two ways without NA' => ['g', 'h', false, true, 'tabyl_gh_no_na'],
        'two ways by a number' => ['g', 'k', true, true, 'tabyl_gk'],
        'two ways by factor and logical' => ['f', 'l', true, true, 'tabyl_fl'],
    ]);

    it('counts as integers and keeps the factor', function () {
        $out = pipe(tabData(), tabyl('f'));

        expect($out->types()['n'])->toBe(Type::Int)
            ->and($out->types()['f'])->toBe(Type::Factor)
            ->and($out->collect()->levels('f'))->toBe(['lo', 'mid', 'hi']);
    });
});
