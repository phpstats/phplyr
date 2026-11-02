<?php

declare(strict_types=1);

namespace Phplyr;

use Closure;

/**
 * @internal Converts a name to snake_case the way janitor's make_clean_names()
 * does through snakecase's to_any_case(case = "snake", sep_in = "\\.",
 * transliterations = "Latin-ASCII", parsing_option = 1, numerals = "asis").
 *
 * Written from snakecase's documentation and from janitor's observed output,
 * without reference to snakecase's source.
 */
final class SnakeCase
{
    /**
     * Separators between words in the input: dots (janitor's sep_in),
     * underscores, and any whitespace.
     */
    private const SEPARATORS = '/[._\s]+/u';

    /**
     * Word boundaries inside a run of letters and digits:
     *  - before an uppercase letter that follows a letter that isn't
     *    uppercase ("myName" -> my|Name, "aB" -> a|B);
     *  - before the last letter of two or more uppercase letters when a
     *    letter that isn't uppercase follows it ("HTTPServer" -> HTTP|Server).
     * Digits and other characters never start or end a boundary, so "x1Ab"
     * and "a1B" stay whole and digits keep to the word they touch.
     */
    private const BOUNDARIES = '/(?<=[^\P{L}\p{Lu}])(?=\p{Lu})|(?<=\p{Lu})(?=\p{Lu}[^\P{L}\p{Lu}])/u';

    /** @param Closure(string): string $ascii transliterates a string to ASCII */
    public static function convert(string $name, Closure $ascii): string
    {
        $words = [];
        foreach (preg_split(self::SEPARATORS, $name, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $chunk) {
            foreach (preg_split(self::BOUNDARIES, $chunk, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
                // Words are found on the original letters, so a letter that
                // transliterates to two ("Æ" to "AE") doesn't add a boundary;
                // only then is each word transliterated and lowercased.
                $word = mb_strtolower($ascii($word), 'UTF-8');
                // A transliteration may contain spaces ("½" to " 1/2"), which
                // separate words like any other whitespace.
                array_push($words, ...(preg_split(self::SEPARATORS, $word, -1, PREG_SPLIT_NO_EMPTY) ?: []));
            }
        }

        return implode('_', $words);
    }
}
