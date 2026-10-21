<?php

declare(strict_types=1);

/*
 * The method chain, phplyr($t)->verb(...), and pipe($t, verb(...)) give the
 * same result as applying each verb's closure in turn, which is what the
 * PHP 8.5 pipe operator does. tests/Pipe compares them with |> itself.
 */

use Phibble\Contracts\Table;
use Phibble\DataFrame;
use Phplyr\Chain;

use function Phplyr\{arrange, desc, filter, group_by, inner_join, mean, mutate, n, phplyr, pipe, pull, slice_max, summarise};

/**
 * Pipelines as [verb, args] steps, so each runs three ways: as closures
 * applied in turn, through pipe(), and as a method chain.
 */
dataset('chain pipelines', fn () => [
    'filter and arrange' => [[
        ['filter', [fn ($r) => $r->score > 3]],
        ['arrange', [desc('score'), 'id']],
    ]],
    'grouped summary' => [[
        ['group_by', ['g']],
        ['summarise', ['n' => n(), 'm' => mean('score', naRm: true)]],
    ]],
    'named options' => [[
        ['mutate', ['z' => fn ($r) => $r->score * 2, 'before' => 'id']],
        ['slice_max', ['score', 'n' => 2, '_by' => 'g']],
    ]],
    'join and count' => [[
        ['inner_join', [panel()->select(['id', 'g'])->rename(['g2' => 'g']), 'by' => 'id']],
        ['count', ['g', 'g2', 'sort' => true]],
    ]],
    'pivot' => [[
        ['select', ['id', 'g', 'year', 'score']],
        ['pivot_wider', ['namesFrom' => 'year', 'valuesFrom' => 'score', 'idCols' => 'g', 'valuesFn' => fn (array $v) => array_sum($v)]],
    ]],
    'non-table result' => [[
        ['group_by', ['g']],
        ['summarise', ['n' => n()]],
        ['pull', ['n']],
    ]],
    'grouping helper' => [[
        ['group_by', ['g']],
        ['group_vars', []],
    ]],
]);

function stepClosure(array $step): Closure
{
    [$name, $args] = $step;

    return ('Phplyr\\'.$name)(...$args);
}

/** The chain spelling of a verb: group_by() is ->groupBy(). */
function chainMethod(string $verb): string
{
    return lcfirst(str_replace('_', '', ucwords($verb, '_')));
}

function expectSameResult(mixed $actual, mixed $expected): void
{
    if ($expected instanceof Table) {
        expect($actual)->toBeInstanceOf(Table::class);
        expect($actual->columns())->toBe($expected->columns());
        expect($actual->groups())->toBe($expected->groups());
        expect($actual->toRows())->toBe($expected->toRows());

        return;
    }
    expect($actual)->toBe($expected);
}

it('gives the same result as applying the verbs in turn for', function (array $steps) {
    $expected = panel();
    foreach ($steps as $step) {
        $expected = stepClosure($step)($expected);
    }

    expectSameResult(pipe(panel(), ...array_map(stepClosure(...), $steps)), $expected);

    $chain = phplyr(panel());
    foreach ($steps as [$name, $args]) {
        $chain = $chain->{chainMethod($name)}(...$args);
    }
    expectSameResult($chain instanceof Chain ? $chain->get() : $chain, $expected);
})->with('chain pipelines');

it('runs a written-out chain', function () {
    $out = phplyr(panel())
        ->filter(fn ($r) => $r->score !== null)
        ->groupBy('g')
        ->summarise(n: n(), m: mean('score'))
        ->arrange(desc('m'))
        ->get();

    $expected = pipe(
        panel(),
        filter(fn ($r) => $r->score !== null),
        group_by('g'),
        summarise(n: n(), m: mean('score')),
        arrange(desc('m')),
    );
    expectSameResult($out, $expected);
});

it('continues the chain while results are tables and returns other results plainly', function () {
    expect(phplyr(panel())->groupBy('g'))->toBeInstanceOf(Chain::class);
    expect(phplyr(panel())->groupBy('g')->get())->toBeInstanceOf(DataFrame::class);
    expect(phplyr(panel())->pull('id'))->toBe(panel()->column('id'));
    expect(phplyr(panel())->groupBy('g')->nGroups())->toBe(3);
    expect(phplyr(panel())->setequal(panel()))->toBeTrue();
});

it('applies closures with pipe() and ->pipe()', function () {
    $double = fn (Table $t) => mutate(score: fn ($r) => $r->score === null ? null : $r->score * 2)($t);
    $first = fn (Table $t) => $t->column('score')[0];

    expect(phplyr(panel())->pipe($double)->get()->column('score'))->toBe(pipe(panel(), $double)->column('score'));
    expect(phplyr(panel())->pipe($double, $first))->toBe(pipe(panel(), $double, $first));
    expect(pipe(3, fn ($x) => $x + 1, fn ($x) => $x * 2))->toBe(8);
    expect(pipe('unchanged'))->toBe('unchanged');
});

it('prints the table, and accepts a chain', function () {
    $chain = phplyr(panel())->head(3);
    expect((string) $chain)->toBe((string) $chain->get());
    expect(phplyr($chain))->toBe($chain);
});

it('runs joins with a table argument', function () {
    $y = panel()->select(['id', 'g']);
    expectSameResult(phplyr(panel())->innerJoin($y, by: ['id', 'g'])->get(), pipe(panel(), inner_join($y, by: ['id', 'g'])));
});

it('rejects names that are not verbs', function (string $name) {
    expect(fn () => phplyr(panel())->{$name}('score'))->toThrow(BadMethodCallException::class, "{$name}() is not a phplyr verb");
})->with(['mean', 'startsWith', 'nonexistent', 'phplyr', 'pipeX']);

it('takes a verb in camelCase and runs the snake_case function', function () {
    expectSameResult(phplyr(panel())->groupBy('g')->summarise(n: n())->get(), pipe(panel(), group_by('g'), summarise(n: n())));
    expectSameResult(phplyr(panel())->sliceMax('score', n: 2, _by: 'g')->get(), pipe(panel(), slice_max('score', n: 2, _by: 'g')));
    expect(phplyr(panel())->groupBy('g')->nGroups())->toBe(3);
    expect(phplyr(panel())->groupBy('g')->groupVars())->toBe(['g']);
});

it('still has the function under its own snake_case name', function () {
    expect(\function_exists('Phplyr\\group_by'))->toBeTrue();
    expect(\function_exists('Phplyr\\groupBy'))->toBeFalse();
    expectSameResult(pipe(panel(), group_by('g'), summarise(n: n())), phplyr(panel())->groupBy('g')->summarise(n: n())->get());
});

it('rejects the function spelling on a chain and names the camelCase one', function (string $snake, string $camel) {
    expect(fn () => phplyr(panel())->{$snake}('g'))
        ->toThrow(BadMethodCallException::class, "{$snake}() is not a phplyr verb; a chained verb is camelCase, so write {$camel}()");
})->with([
    ['group_by', 'groupBy'],
    ['slice_max', 'sliceMax'],
    ['pivot_longer', 'pivotLonger'],
    ['n_groups', 'nGroups'],
    ['top_n', 'topN'],
    ['separate_wider_delim', 'separateWiderDelim'],
]);

it('spells every verb for a chain so that it maps back to the function', function () {
    $verbs = array_filter(
        get_defined_functions()['user'],
        fn (string $f) => str_starts_with($f, 'phplyr\\')
            && (string) (new \ReflectionFunction($f))->getReturnType() === 'Closure',
    );
    expect($verbs)->not->toBeEmpty();
    foreach ($verbs as $f) {
        $verb = (new \ReflectionFunction($f))->getShortName();
        $camel = chainMethod($verb);
        expect(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $camel)))->toBe($verb, $verb);
    }
});

it('lists every verb in its @method docblock', function () {
    $php = escapeshellarg(PHP_BINARY);
    exec("{$php} ".escapeshellarg(dirname(__DIR__).'/bin/chain-docblock').' --check 2>&1', $out, $status);
    expect($status)->toBe(0, implode("\n", $out));
});
