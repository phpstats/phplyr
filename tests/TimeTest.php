<?php

declare(strict_types=1);

// phibble 0.6's time columns (Phibble\Time, R's hms) through phplyr's
// verbs, in memory and from SQLite. The expected rows follow dplyr 1.2.1
// with hms 1.1.4.

use Phibble\DataFrame;
use Phibble\Time;

use function Phibble\tbl;
use function Phplyr\{arrange, between, coalesce, col, collect, complete, count, desc, distinct, filter, group_by, inner_join, join_by, lag, left_join, max, min, min_rank, mutate, pipe, select, slice_min, summarize, where};

/** A column as text, with null for NA. */
function timeText(Phibble\Contracts\Table $t, string $column): array
{
    return array_map(fn ($v) => $v === null ? null : (string) $v, $t->column($column));
}

function timeTable(): DataFrame
{
    return DataFrame::fromRows([
        ['id' => 1, 'g' => 'a', 'tm' => Time::of(7, 30)],
        ['id' => 2, 'g' => 'a', 'tm' => Time::of(9, 5)],
        ['id' => 3, 'g' => 'b', 'tm' => Time::of(7, 30)],
        ['id' => 4, 'g' => 'b', 'tm' => null],
        ['id' => 5, 'g' => 'b', 'tm' => Time::of(25)],
    ]);
}

it('sorts, groups, and summarizes times as dplyr does with hms', function () {
    $t = timeTable();

    expect(pipe($t, arrange('tm'))->column('id'))->toBe([1, 3, 2, 5, 4])
        ->and(pipe($t, arrange(desc('tm')))->column('id'))->toBe([5, 2, 1, 3, 4]);

    $n = pipe($t, count('tm'));
    expect(timeText($n, 'tm'))->toBe(['07:30:00', '09:05:00', '25:00:00', null])
        ->and($n->column('n'))->toBe([2, 1, 1, 1]);
    expect(timeText(pipe($t, distinct('tm')), 'tm'))->toBe(['07:30:00', '09:05:00', null, '25:00:00']);

    $s = pipe($t, group_by('g'), summarize(lo: min('tm'), hi: max('tm')));
    expect(timeText($s, 'lo'))->toBe(['07:30:00', null])
        ->and(timeText($s, 'hi'))->toBe(['09:05:00', null]);
})->skip(fn () => ! class_exists(Time::class), 'needs phibble 0.6');

it('ranks, lags, and compares times', function () {
    $t = pipe(timeTable(), mutate(
        r: min_rank('tm'),
        prev: lag('tm'),
        early: between('tm', Time::of(7), Time::of(8)),
        filled: coalesce('tm', Time::of(0)),
    ));

    expect($t->column('r'))->toBe([1, 3, 1, null, 4])
        ->and(timeText($t, 'prev'))->toBe([null, '07:30:00', '09:05:00', '07:30:00', null])
        ->and($t->column('early'))->toBe([true, false, true, null, false])
        ->and(timeText($t, 'filled'))->toBe(['07:30:00', '09:05:00', '07:30:00', '00:00:00', '25:00:00'])
        ->and(pipe(timeTable(), filter(col('tm')->gt(Time::of(8))))->column('id'))->toBe([2, 5])
        ->and(pipe(timeTable(), slice_min('tm', n: 1))->column('id'))->toBe([1, 3])
        ->and(pipe(timeTable(), select(where('time')))->columns())->toBe(['tm']);
})->skip(fn () => ! class_exists(Time::class), 'needs phibble 0.6');

it('joins on times', function () {
    $labels = DataFrame::fromRows([['tm' => Time::of(7, 30), 'label' => 'early'], ['tm' => Time::of(9, 5), 'label' => 'late']]);
    expect(pipe(timeTable(), left_join($labels, by: 'tm'))->column('label'))->toBe(['early', 'late', 'early', null, null]);

    $starts = DataFrame::fromRows([['start' => Time::of(7, 30), 'label' => 'early'], ['start' => Time::of(9, 5), 'label' => 'late']]);
    $j = pipe(timeTable(), inner_join($starts, by: join_by(col('tm')->ge(col('start')))));
    expect($j->column('id'))->toBe([1, 2, 2, 3, 5, 5])
        ->and($j->column('label'))->toBe(['early', 'early', 'late', 'early', 'early', 'late']);

    $c = pipe(timeTable(), complete('g', 'tm'));
    expect($c->nrow())->toBe(8)
        ->and(timeText($c, 'tm'))->toBe(['07:30:00', '09:05:00', '25:00:00', null, '07:30:00', '09:05:00', '25:00:00', null]);
})->skip(fn () => ! class_exists(Time::class), 'needs phibble 0.6');

it('reads SQLite time text as times and runs verbs on them', function () {
    $pdo = new PDO('sqlite::memory:');
    $pdo->exec("CREATE TABLE s (id INTEGER, g TEXT, tm TEXT);
        INSERT INTO s VALUES (1, 'a', '07:30:00'), (2, 'a', '09:05:00'), (3, 'b', '07:30:00'), (4, 'b', NULL), (5, 'b', '25:00:00');
        CREATE TABLE k (tm TEXT, label TEXT);
        INSERT INTO k VALUES ('07:30:00', 'early'), ('09:05:00', 'late');");
    $s = tbl($pdo, 's');

    expect(pipe($s, collect())->column('tm')[0])->toBeInstanceOf(Time::class)
        ->and(pipe($s, arrange('tm'), collect())->column('id'))->toBe([1, 3, 2, 5, 4])
        ->and(pipe($s, filter(col('tm')->gt(Time::of(8))), collect())->column('id'))->toBe([2, 5])
        ->and(pipe($s, left_join(tbl($pdo, 'k'), by: 'tm'), collect())->column('label'))->toBe(['early', 'late', 'early', null, null]);

    $n = pipe($s, count('tm'), collect());
    expect(timeText($n, 'tm'))->toBe(['07:30:00', '09:05:00', '25:00:00', null])
        ->and($n->column('n'))->toBe([2, 1, 1, 1]);
})->skip(fn () => ! class_exists(Time::class) || ! extension_loaded('pdo_sqlite'), 'needs phibble 0.6 and pdo_sqlite');
