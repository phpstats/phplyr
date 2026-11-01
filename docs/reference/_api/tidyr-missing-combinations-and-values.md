### `complete()` {#complete}

```php
complete(mixed ...$items): Closure
```

Add missing combinations, filling other columns with NA (or with fill). `complete('year', 'type', fill: ['n' => 0])`. Option: explicit (default true; false fills only the added rows).

Returns a function of a table, which returns a table. Chain method: `->complete()`. [Example](#missing-combinations-and-values).

### `expand()` {#expand}

```php
expand(mixed ...$items): Closure
```

Every combination of the given columns, per group: `expand('year', 'type')`, `expand(nesting('id', 'name'), 'year')`, `expand(year: range(2000, 2005))`.

Returns a function of a table, which returns a table. Chain method: `->expand()`.

### `nesting()` {#nesting}

```php
nesting(mixed ...$columns): Nesting
```

Only the combinations that occur in the data, for `complete()` and `expand()`.

Returns `Nesting`.

### `crossing()` {#crossing}

```php
crossing(array ...$columns): Phibble\DataFrame
```

Every combination of unique values (sorted), as a new table: `crossing(x: [1, 2], y: ['a', 'b'])`.

Returns `Phibble\DataFrame`.

### `expand_grid()` {#expand_grid}

```php
expand_grid(array ...$columns): Phibble\DataFrame
```

Every combination of the values as given, first column varying slowest.

Returns `Phibble\DataFrame`.

### `full_seq()` {#full_seq}

```php
full_seq(array $x, int|float $period, float $tol = 1.0E-6): array
```

Every value from the smallest to the largest of x in steps of period: `full_seq([1, 5, 3], 1)` is [1, 2, 3, 4, 5]. Values off the grid (beyond tol) are an error.

Returns `array`. Throws `InvalidArgumentException` on invalid arguments.

### `fill()` {#fill}

```php
fill(mixed ...$cols): Closure
```

Fill NA with the previous (or next) value, within groups: `fill('price', _direction: 'down'|'up'|'downup'|'updown')`.

Returns a function of a table, which returns a table. Chain method: `->fill()`. Throws `InvalidArgumentException` on invalid arguments. An unknown underscored option throws `InvalidArgumentException`. [Example](#missing-combinations-and-values).

### `drop_na()` {#drop_na}

```php
drop_na(mixed ...$cols): Closure
```

Drop rows with NA in any of the columns (any column when none are given).

Returns a function of a table, which returns a table. Chain method: `->dropNa()`.

### `replace_na()` {#replace_na}

```php
replace_na(array $replace): Closure
```

Replace NA per column: `replace_na(['n' => 0, 'label' => 'unknown'])`.

Returns a function of a table, which returns a table. Chain method: `->replaceNa()`. [Example](#missing-combinations-and-values).

### `uncount()` {#uncount}

```php
uncount(mixed $weights, bool $_remove = true, ?string $_id = null): Closure
```

Repeat each row by a weight column: `uncount('n')`. Options: `_remove` (drop the weight column, default true), `_id` (add a 1-based copy number).

Returns a function of a table, which returns a table. Chain method: `->uncount()`. Throws `InvalidArgumentException` on invalid arguments.
