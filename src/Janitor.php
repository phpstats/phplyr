<?php

declare(strict_types=1);

namespace Phplyr;

use Phibble\Contracts\Table;
use Phibble\DataFrame;
use Phibble\Type;

/** @internal Helpers for the janitor functions. */
final class Janitor
{
    private const PUNCT = '[\h\s\p{P}\p{S}\p{Z}\p{C}]';

    private const RESERVED = ['if', 'else', 'repeat', 'while', 'function', 'for', 'next', 'break', 'TRUE', 'FALSE', 'NULL',
        'Inf', 'NaN', 'NA', 'NA_integer_', 'NA_real_', 'NA_character_', 'NA_complex_', 'in'];

    /** janitor's replacements, transliteration, and punctuation cleanup, before make.names(). */
    public static function prepare(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }
        $name = strtr($name, ["'" => '', '"' => '', '%' => '_percent_', '#' => '_number_']);
        $name = self::ascii($name);
        $name = preg_replace('/\A'.self::PUNCT.'*(.*)$/u', '$1', $name) ?? $name;

        return preg_replace('/'.self::PUNCT.'+/u', '.', $name, 1) ?? $name;
    }

    /** R's make.names() for one name (NA becomes "NA."). */
    public static function makeName(?string $name): string
    {
        if ($name === null) {
            return 'NA.';
        }
        $name = preg_replace('/[^\p{L}\p{N}._]/u', '.', $name) ?? $name;
        if (! preg_match('/\A(\p{L}|\.(?!\p{Nd}))/u', $name)) {
            $name = 'X'.$name;
        }

        return \in_array($name, self::RESERVED, true) ? $name.'.' : $name;
    }

    /**
     * snakecase::to_any_case(case = "snake", sep_in = "\\.",
     * parsing_option = 1, numerals = "asis"), as janitor calls it.
     */
    public static function snake(string $name): string
    {
        $s = preg_replace('/\h/u', '_', $name);
        $s = \str_replace('.', '_', $s);
        $s = preg_replace('/(?<=[^_|\p{Nd}])(\p{Nd})/u', '_ $1', $s);
        $s = preg_replace('/(\p{Nd})(?=[^_|\p{Nd}])/u', '$1 _', $s);
        $s = preg_replace('/(\p{Lu}\p{Ll}+)/u', '_$1_', $s);
        $s = preg_replace('/(\p{Nd}+)/u', '_$1_', $s);
        $s = preg_replace('/(\p{Lu}{2,})/u', '_$1_', $s);
        $s = preg_replace('/((?<!\p{Lu})\p{Lu}(?!\p{L}))/u', '_$1_', $s);
        $s = preg_replace('/([^\p{L}\p{N}])/u', '_$1_', $s);
        $s = preg_replace('/_+/', '_', $s);
        $s = preg_replace('/^_|_$/', '', $s);
        $s = mb_strtolower(self::ascii($s));
        $s = \str_replace(' ', '', $s);

        return preg_replace('/_(?![\p{L}\p{N}])|(?<![\p{L}\p{N}])_/u', '', $s);
    }

    /**
     * janitor's de-duplication: the k-th repeat of a name gets "_k", until
     * no name repeats.
     *
     * @param list<string> $names
     * @return list<string>
     */
    public static function dedupe(array $names): array
    {
        while (\count(array_unique($names)) < \count($names)) {
            $seen = [];
            foreach ($names as $i => $name) {
                $seen[$name] = ($seen[$name] ?? 0) + 1;
                if ($seen[$name] > 1) {
                    $names[$i] = $name.'_'.$seen[$name];
                }
            }
        }

        return $names;
    }

    /** Transliterate to ASCII as janitor does through stringi (needs ext-intl; iconv otherwise). */
    private static function ascii(string $s): string
    {
        static $transliterator = null;
        if (class_exists(\Transliterator::class)) {
            $transliterator ??= \Transliterator::create('Any-Latin;Greek-Latin;Any-NFKD;Any-NFC;Latin-ASCII');
            if ($transliterator !== null) {
                $out = $transliterator->transliterate($s);

                return $out === false ? $s : $out;
            }
        }
        if (\function_exists('iconv')) {
            $out = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);

            return $out === false ? $s : $out;
        }

        return $s;
    }

    public static function tabyl1(Table $t, string $var, bool $showNa, bool $showMissingLevels): DataFrame
    {
        [$values, $counts] = self::tally($t, $var, $showMissingLevels);
        $hasNa = \in_array(null, $values, true);
        if ($hasNa && ! $showNa) {
            $i = array_search(null, $values, true);
            array_splice($values, $i, 1);
            array_splice($counts, $i, 1);
            $hasNa = false;
        }
        $total = array_sum($counts);
        $columns = [
            $var => $values,
            'n' => $counts,
            'percent' => array_map(fn ($n) => $total > 0 ? $n / $total : NAN, $counts),
        ];
        if ($hasNa) {
            $valid = $total - $counts[array_search(null, $values, true)];
            $columns['valid_percent'] = array_map(fn ($v, $n) => $v === null ? null : $n / $valid, $values, $counts);
        }

        return self::withVarType(DataFrame::fromColumns($columns, ['n' => Type::Int, 'percent' => Type::Float] + ($hasNa ? ['valid_percent' => Type::Float] : [])), $t, [$var]);
    }

    public static function tabyl2(Table $t, string $var1, string $var2, bool $showNa, bool $showMissingLevels): DataFrame
    {
        if (! $showNa) {
            $t = $t->filter(fn ($r) => $r[$var1] !== null && $r[$var2] !== null);
        }
        [$rows] = self::tally($t, $var1, $showMissingLevels);
        [$cols] = self::tally($t, $var2, $showMissingLevels);
        $labels = [];
        foreach ($cols as $c) {
            $labels[Internal::key($c)] = match (true) {
                $c === null => 'NA_',
                $c === '' => 'emptystring_',
                default => self::label($c),
            };
        }
        // As in janitor, "emptystring_" and "NA_" go last.
        uasort($labels, fn ($a, $b) => ($a === 'NA_' ? 2 : ($a === 'emptystring_' ? 1 : 0)) <=> ($b === 'NA_' ? 2 : ($b === 'emptystring_' ? 1 : 0)));
        $rowIndex = [];
        foreach ($rows as $i => $r) {
            $rowIndex[Internal::key($r)] = $i;
        }
        $counts = array_fill_keys(array_keys($labels), array_fill(0, \count($rows), 0));
        $a = $t->column($var1);
        $b = $t->column($var2);
        foreach ($a as $i => $v) {
            $counts[Internal::key($b[$i])][$rowIndex[Internal::key($v)]]++;
        }
        $columns = [$var1 => $rows];
        $types = [];
        foreach ($labels as $key => $label) {
            $columns[$label] = $counts[$key];
            $types[$label] = Type::Int;
        }

        return self::withVarType(DataFrame::fromColumns($columns, $types), $t, [$var1]);
    }

    /**
     * Distinct values of a column in dplyr's count() order, with their counts.
     *
     * @return array{list<mixed>, list<int>}
     */
    private static function tally(Table $t, string $var, bool $showMissingLevels): array
    {
        $counts = [];
        $values = [];
        foreach ($t->column($var) as $v) {
            $k = Internal::key($v);
            $values[$k] ??= $v;
            $counts[$k] = ($counts[$k] ?? 0) + 1;
        }
        if (($t->types()[$var] ?? null) === Type::Factor) {
            $levels = $t->collect()->levels($var);
            $ordered = [];
            foreach ($levels as $level) {
                $k = Internal::key($level);
                if (isset($values[$k]) || $showMissingLevels) {
                    $ordered[$k] = $level;
                }
            }
            if (\array_key_exists("\0NA", $values)) {
                $ordered["\0NA"] = null;
            }
            $values = $ordered;
        } else {
            uasort($values, function ($a, $b) {
                if ($a === null || $b === null) {
                    return ($a === null) <=> ($b === null);
                }

                return \is_string($a) && \is_string($b) ? strcmp($a, $b) : $a <=> $b;
            });
        }

        return [array_values($values), array_map(fn ($k) => $counts[$k] ?? 0, array_keys($values))];
    }

    /** How R's as.character() writes a value used as a column name. */
    private static function label(mixed $v): string
    {
        return match (true) {
            \is_bool($v) => $v ? 'TRUE' : 'FALSE',
            \is_float($v) && is_finite($v) && \floor($v) === $v && \abs($v) < 1e15 => (string) (int) $v,
            $v instanceof \DateTimeInterface => $v->format('Y-m-d'),
            default => (string) $v,
        };
    }

    /** Give the tabulated columns their source types (and factor levels). */
    private static function withVarType(DataFrame $out, Table $t, array $vars): DataFrame
    {
        $types = $t->types();
        foreach ($vars as $var) {
            if (($types[$var] ?? null) === Type::Factor) {
                $out = $out->asFactor($var, $t->collect()->levels($var));
            } elseif (isset($types[$var]) && ($out->types()[$var] ?? null) !== $types[$var]) {
                $out = DataFrame::fromColumns(array_combine($out->columns(), array_map(fn ($c) => $out->column($c), $out->columns())), [$var => $types[$var]] + $out->types());
            }
        }

        return $out;
    }
}
