<?php

declare(strict_types=1);

/*
 * lubridate helpers, re-exported from Phibble\Expr's registry. Periods such
 * as months(1) are expressions, so col('d')->add(months(1)) works; month or
 * year arithmetic that lands on an invalid date gives NA, as in lubridate.
 * Phibble\Expr\Clock::freeze() fixes today() and now() in tests.
 */

namespace Phplyr;

use Phibble\Expr\Lit;

/** @internal tz as a named option, only when given. */
function _tz(?string $tz): array
{
    return $tz === null ? [] : ['tz' => $tz];
}

// Parsing
/** Parse year-month-day strings to dates, with any separator. */
function ymd(mixed $x, ?string $tz = null): Vec { return Internal::call('ymd', [$x], _tz($tz)); }
/** Parse day-month-year strings to dates, with any separator. */
function dmy(mixed $x, ?string $tz = null): Vec { return Internal::call('dmy', [$x], _tz($tz)); }
/** Parse month-day-year strings to dates, with any separator. */
function mdy(mixed $x, ?string $tz = null): Vec { return Internal::call('mdy', [$x], _tz($tz)); }
/** Parse year-month-day hour:minute:second strings to datetimes, in UTC or tz. */
function ymd_hms(mixed $x, ?string $tz = null): Vec { return Internal::call('ymd_hms', [$x], _tz($tz)); }
/** Parse day-month-year hour:minute:second strings to datetimes, in UTC or tz. */
function dmy_hms(mixed $x, ?string $tz = null): Vec { return Internal::call('dmy_hms', [$x], _tz($tz)); }
/** Parse month-day-year hour:minute:second strings to datetimes, in UTC or tz. */
function mdy_hms(mixed $x, ?string $tz = null): Vec { return Internal::call('mdy_hms', [$x], _tz($tz)); }

/** Parse with lubridate orders: parse_date_time('when', ['Ymd HMS', 'dmY HMS']). */
function parse_date_time(mixed $x, string|array $orders, ?string $tz = null): Vec
{
    return Internal::call('parse_date_time', [$x, new Lit(array_values((array) $orders))], _tz($tz));
}

// Parts
/** The year of a date or datetime. */
function year(mixed $x): Vec { return Internal::call('year', [$x]); }
/** The month, 1-12. */
function month(mixed $x): Vec { return Internal::call('month', [$x]); }
/** The day of the month. */
function day(mixed $x): Vec { return Internal::call('day', [$x]); }

/** Day of the week, 1-7; Sunday is 1 unless weekStart says otherwise (1 = Monday). */
function wday(mixed $x, ?int $weekStart = null): Vec { return Internal::call('wday', [$x], $weekStart === null ? [] : ['week_start' => $weekStart]); }
/** The day of the year, 1-366. */
function yday(mixed $x): Vec { return Internal::call('yday', [$x]); }
/** The quarter, 1-4. */
function quarter(mixed $x): Vec { return Internal::call('quarter', [$x]); }
/** The hour of a datetime, 0-23. */
function hour(mixed $x): Vec { return Internal::call('hour', [$x]); }
/** The minute of a datetime, 0-59. */
function minute(mixed $x): Vec { return Internal::call('minute', [$x]); }
/** The second of a datetime, 0-59. */
function second(mixed $x): Vec { return Internal::call('second', [$x]); }

// Rounding: unit is 'second', 'minute', 'hour', 'day', 'week', 'month', 'quarter' or 'year', optionally with a multiple ('15 minutes').
/** Round down to a unit: 'second', 'minute', 'hour', 'day', 'week', 'month', 'quarter', or 'year', optionally with a multiple such as '15 minutes'. */
function floor_date(mixed $x, string $unit, ?int $weekStart = null): Vec { return Internal::call('floor_date', [$x, $unit], $weekStart === null ? [] : ['week_start' => $weekStart]); }
/** Round up to a unit, as floor_date(). */
function ceiling_date(mixed $x, string $unit, ?int $weekStart = null): Vec { return Internal::call('ceiling_date', [$x, $unit], $weekStart === null ? [] : ['week_start' => $weekStart]); }
/** Round to the nearest unit, as floor_date(). */
function round_date(mixed $x, string $unit, ?int $weekStart = null): Vec { return Internal::call('round_date', [$x, $unit], $weekStart === null ? [] : ['week_start' => $weekStart]); }

// Periods, for date arithmetic.
/** A period of n seconds, for date arithmetic. */
function seconds(mixed $n = 1): Vec { return Internal::call('seconds', [$n], columns: []); }
/** A period of n minutes. */
function minutes(mixed $n = 1): Vec { return Internal::call('minutes', [$n], columns: []); }
/** A period of n hours. */
function hours(mixed $n = 1): Vec { return Internal::call('hours', [$n], columns: []); }
/** A period of n days. */
function days(mixed $n = 1): Vec { return Internal::call('days', [$n], columns: []); }
/** A period of n weeks. */
function weeks(mixed $n = 1): Vec { return Internal::call('weeks', [$n], columns: []); }
/** A period of n months; adding one to a day the target month lacks gives NA, as lubridate does. */
function months(mixed $n = 1): Vec { return Internal::call('months', [$n], columns: []); }
/** A period of n years. */
function years(mixed $n = 1): Vec { return Internal::call('years', [$n], columns: []); }

/** The difference x - y in units 'secs', 'mins', 'hours', 'days', or 'weeks', as a double. */
function difftime(mixed $x, mixed $y, ?string $units = null): Vec
{
    return Internal::call('difftime', [$x, $y], $units === null ? [] : ['units' => $units], columns: [0, 1]);
}

/** The same instant in another time zone. */
function with_tz(mixed $x, string $tz): Vec { return Internal::call('with_tz', [$x, $tz]); }

/** The same clock time in another time zone. */
function force_tz(mixed $x, string $tz): Vec { return Internal::call('force_tz', [$x, $tz]); }

/** The current date, in the system time zone or tz. */
function today(?string $tz = null): Vec { return Internal::call('today', [], _tz($tz)); }
/** The current datetime, in the system time zone or tz. */
function now(?string $tz = null): Vec { return Internal::call('now', [], _tz($tz)); }
