### `select()` {#select}

```php
select(mixed ...$columns): Closure
```

Keep or drop columns: names, lists of names, selectors such as `starts_with('x')` or `except('id')`. Named arguments rename: `select(id: 'ID')`.

Returns a function of a table, which returns a table. Chain method: `->select()`. [Example](#columns).

### `rename()` {#rename}

```php
rename(string ...$map): Closure
```

Rename columns, new name as the argument name: `rename(price: 'cost')`.

Returns a function of a table, which returns a table. Chain method: `->rename()`.

### `rename_with()` {#rename_with}

```php
rename_with(Closure $fn, mixed $cols = null): Closure
```

Rename selected columns (all by default) with a function of the old name.

Returns a function of a table, which returns a table. Chain method: `->renameWith()`. [Example](#columns).

### `relocate()` {#relocate}

```php
relocate(mixed ...$columns): Closure
```

Move columns: to the front by default, or `_before` / `_after` a column. Named arguments rename while moving.

Returns a function of a table, which returns a table. Chain method: `->relocate()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#columns).

### `mutate()` {#mutate}

```php
mutate(mixed ...$columns): Closure
```

Add, replace, or drop (with null) columns, named by argument: `mutate(log_price: fn ($r) => log($r->price), rank: min_rank('price'))`.

Values may be a row closure `fn ($r, $group)`, a vector helper (lag, cumsum, mean...; summaries recycle within groups), a list with one value per row, a scalar, or null to drop the column. `across(...)` may be passed first.

Options: `_by`, `_keep` ('all', 'none'), `_before`, `_after`.

Returns a function of a table, which returns a table. Chain method: `->mutate()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#columns).

### `transmute()` {#transmute}

```php
transmute(mixed ...$columns): Closure
```

Like `mutate()`, keeping only the grouping columns and the new columns. A bare column name keeps that column: `transmute('id', half: ...)`.

Returns a function of a table, which returns a table. Chain method: `->transmute()`. An unknown underscored option throws `InvalidArgumentException`.

### `pull()` {#pull}

```php
pull(string|int $var = -1, ?string $name = null): Closure
```

Extract one column as a PHP list: by name, or by position like dplyr (1 is the first column, -1 the last; the default). With `$name`, the list is keyed by that column's values.

Returns a function of a table, which returns a PHP list of the column's values. Chain method: `->pull()`. Throws `OutOfRangeException` on invalid arguments. [Example](#columns).
