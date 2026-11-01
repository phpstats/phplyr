### `summarise()` {#summarise}

```php
summarise(mixed ...$aggregates): Closure
```

One row per group. Each named argument is a summary: a helper such as `n()` or `mean('x')`, or any `fn (Table $group) => value`. `across(...)` may be passed first. Options: `_by`, `_groups` ('drop_last', 'drop', 'keep').

Returns a function of a table, which returns a table. Chain method: `->summarise()`. An unknown underscored option throws `InvalidArgumentException`.

### `summarize()` {#summarize}

```php
summarize(mixed ...$aggregates): Closure
```

Alias of `summarise()`.

Returns a function of a table, which returns a table. Chain method: `->summarize()`. [Example](#summaries).

### `reframe()` {#reframe}

```php
reframe(mixed ...$aggregates): Closure
```

Like `summarise()`, but each summary may return any number of values (a list); they are recycled to a common length per group. The result is ungrouped. Option: `_by`.

Returns a function of a table, which returns a table. Chain method: `->reframe()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#summaries).

### `count()` {#count}

```php
count(mixed ...$args): Closure
```

Count rows per combination of columns, within any existing groups. The result keeps the original grouping, like dplyr. Options: wt (column to sum instead of counting), sort, name.

Returns a function of a table, which returns a table. Chain method: `->count()`. [Example](#summaries).

### `tally()` {#tally}

```php
tally(mixed $wt = null, bool $sort = false, ?string $name = null): Closure
```

Count rows in each group (`summarise(n = n())`). Options: wt, sort, name.

Returns a function of a table, which returns a table. Chain method: `->tally()`.

### `add_count()` {#add_count}

```php
add_count(mixed ...$args): Closure
```

Like `count()`, but adds the count as a column instead of summarising.

Returns a function of a table, which returns a table. Chain method: `->addCount()`.

### `add_tally()` {#add_tally}

```php
add_tally(mixed $wt = null, bool $sort = false, ?string $name = null): Closure
```

Like `tally()`, but adds the count as a column.

Returns a function of a table, which returns a table. Chain method: `->addTally()`.

### `glimpse()` {#glimpse}

```php
glimpse(int $width = 80): Closure
```

Print a compact column-by-column summary and return the table unchanged, so it can sit in the middle of a pipeline.

Returns a function of a table, which returns the table unchanged, after printing it. Chain method: `->glimpse()`.
