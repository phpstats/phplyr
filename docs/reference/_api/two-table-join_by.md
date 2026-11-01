### `join_by()` {#join_by}

```php
join_by(mixed ...$conditions): JoinBy
```

A join specification, as dplyr's `join_by()`. Conditions are column names (equality on the same name), ['x' =&gt; 'y'] or [x, op, y] arrays, or comparisons where the left side is x's column and the right side y's:

`join_by('id', col('t')->ge(col('start')))` `join_by('id', between('t', 'start', 'end'))`      // bounds are y's columns join_by('id', `closest(col('t')->ge(col('start')))`) `join_by(overlaps('start', 'end', 'from', 'to'))`

Every condition must hold. Inequality and rolling joins keep the key columns of both tables, as in dplyr.

Returns `JoinBy`.

### `closest()` {#closest}

```php
closest(mixed $condition): Closest
```

A rolling join: keep only the closest match, as in join_by(`closest(col('t')->ge(col('start')))`).

Returns `Closest`.

### `within()` {#within}

```php
within(string $xLower, string $xUpper, string $yLower, string $yUpper): array
```

x's interval [xLower, xUpper] lies within y's [yLower, yUpper].

Returns `array`.

### `overlaps()` {#overlaps}

```php
overlaps(string $xLower, string $xUpper, string $yLower, string $yUpper): array
```

x's interval [xLower, xUpper] overlaps y's [yLower, yUpper].

Returns `array`.
