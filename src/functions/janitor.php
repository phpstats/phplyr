<?php

declare(strict_types=1);

namespace Phplyr;

use Closure;
use Phibble\Contracts\Table;

/**
 * janitor's make_clean_names(): snake_case names that are unique, ASCII, and
 * start with a letter. '%' becomes "percent", '#' becomes "number", accents
 * and other scripts are transliterated (with ext-intl, as janitor does with
 * stringi), names that would start with a digit get an "x" prefix, and
 * repeats get _2, _3, ... suffixes.
 *
 * @param list<string|null> $names
 * @return list<string>
 */
function make_clean_names(array $names): array
{
    $cleaned = array_map(fn ($name) => Janitor::snake(Janitor::makeName(Janitor::prepare($name))), array_values($names));

    return Janitor::dedupe($cleaned);
}

/** Rename every column with make_clean_names(), as janitor's clean_names(). */
function clean_names(): Closure
{
    return function (Table $t) {
        $old = $t->columns();
        $new = make_clean_names($old);
        $map = [];
        foreach ($old as $i => $name) {
            if ($new[$i] !== $name) {
                $map[$new[$i]] = $name;
            }
        }

        return $map === [] ? $t : $t->rename($map);
    };
}

/**
 * Drop rows and/or columns that are entirely NA, as janitor's
 * remove_empty(). With a cutoff below 1 (for rows or cols alone), keep those
 * whose share of non-NA values is above the cutoff.
 *
 * @param string|list<string> $which 'rows', 'cols', or both
 */
function remove_empty(string|array $which = ['rows', 'cols'], float $cutoff = 1.0): Closure
{
    $which = (array) $which;
    if (array_diff($which, ['rows', 'cols']) !== [] || $which === []) {
        throw new \InvalidArgumentException("remove_empty(): which must be 'rows', 'cols', or both.");
    }
    if ($cutoff <= 0.0 || $cutoff > 1.0) {
        throw new \InvalidArgumentException('remove_empty(): cutoff must be > 0 and <= 1.');
    }
    if (\count($which) > 1 && $cutoff !== 1.0) {
        throw new \InvalidArgumentException("remove_empty(): use cutoff with only one of 'rows' or 'cols'.");
    }

    return function (Table $t) use ($which, $cutoff) {
        $isNa = fn ($v) => $v === null || (\is_float($v) && is_nan($v));
        if (\in_array('rows', $which, true)) {
            $columns = array_map(fn ($c) => $t->column($c), $t->columns());
            $ncol = \count($columns);
            $keep = [];
            for ($i = 0, $n = $t->nrow(); $i < $n; $i++) {
                $present = 0;
                foreach ($columns as $values) {
                    $present += $isNa($values[$i]) ? 0 : 1;
                }
                if ($cutoff === 1.0 ? $present !== 0 : $present / $ncol > $cutoff) {
                    $keep[] = $i;
                }
            }
            $t = $t->take($keep);
        }
        if (\in_array('cols', $which, true)) {
            $nrow = $t->nrow();
            $keep = [];
            foreach ($t->columns() as $c) {
                $present = \count(array_filter($t->column($c), fn ($v) => ! $isNa($v)));
                if ($cutoff === 1.0 ? $present !== 0 : $present / $nrow > $cutoff) {
                    $keep[] = $c;
                }
            }
            $t = $t->select(array_values(array_unique([...$t->groups(), ...$keep])));
        }

        return $t;
    };
}

/**
 * Rows whose values in these columns (all columns when none are given)
 * appear more than once, as janitor's get_dupes(): the columns, then
 * dupe_count, then the rest, sorted by dupe_count (descending) and the
 * columns. Grouping is kept but not used.
 */
function get_dupes(mixed ...$columns): Closure
{
    return function (Table $t) use ($columns) {
        $groups = $t->groups();
        $t = $t->ungroup();
        $vars = $columns === [] ? $t->columns() : Selector::select($columns, $t);
        $out = pipe(
            $t,
            add_count(...[...$vars, 'name' => 'dupe_count']),
            filter(fn ($r) => $r->dupe_count > 1),
            select(...$vars, ...['dupe_count'], ...[everything()]),
            arrange(desc('dupe_count'), ...$vars),
        );

        return Internal::regroup($out, $groups, $t);
    };
}

/**
 * janitor's tabyl() for one or two columns. One column gives its values with
 * n and percent (and valid_percent, which leaves NA out, when there are NAs).
 * Two columns give a crosstab of counts: one row per value of the first, one
 * column per value of the second ("NA_" for NA, "emptystring_" for "").
 * Values sort as dplyr's count() does, with NA last and factors in level order.
 */
function tabyl(string $var1, ?string $var2 = null, bool $showNa = true, bool $showMissingLevels = true): Closure
{
    return function (Table $t) use ($var1, $var2, $showNa, $showMissingLevels) {
        $t = $t->ungroup();

        return $var2 === null
            ? Janitor::tabyl1($t, $var1, $showNa, $showMissingLevels)
            : Janitor::tabyl2($t, $var1, $var2, $showNa, $showMissingLevels);
    };
}
