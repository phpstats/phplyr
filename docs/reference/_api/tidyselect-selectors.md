### `starts_with()` {#starts_with}

```php
starts_with(array|string $prefix, bool $ignoreCase = true): Selector
```

Columns whose names start with a prefix (case-insensitive by default).

Returns `Selector`. [Pivot between wide and long](../examples/pivot.qmd).

### `ends_with()` {#ends_with}

```php
ends_with(array|string $suffix, bool $ignoreCase = true): Selector
```

Columns whose names end with a suffix (case-insensitive by default).

Returns `Selector`. [Example](#across-section).

### `contains()` {#contains}

```php
contains(array|string $needle, bool $ignoreCase = true): Selector
```

Columns whose names contain a string (case-insensitive by default).

Returns `Selector`.

### `matches()` {#matches}

```php
matches(string $pattern, bool $ignoreCase = true): Selector
```

Columns whose names match a regular expression. Bare patterns are wrapped in delimiters; case-insensitive by default.

Returns `Selector`.

### `num_range()` {#num_range}

```php
num_range(string $prefix, int $from, int $to, int $width = 0): Selector
```

Columns named prefix + number for each number: `num_range('x', 1, 3)`.

Returns `Selector`.

### `everything()` {#everything}

```php
everything(): Selector
```

Every column.

Returns `Selector`.

### `last_col()` {#last_col}

```php
last_col(int $offset = 0): Selector
```

The last column, or the one `$offset` before it.

Returns `Selector`. Throws `OutOfRangeException` on invalid arguments.

### `all_of()` {#all_of}

```php
all_of(array $names): Selector
```

Exactly these columns; all must exist.

Returns `Selector`.

### `any_of()` {#any_of}

```php
any_of(array $names): Selector
```

These columns, skipping any that don't exist.

Returns `Selector`.

### `where()` {#where}

```php
where(Closure|Phibble\Type|string $predicate): Selector
```

Columns for which a predicate holds. The predicate receives (list `$values`, Type `$type`, string `$name`). Shortcuts: a Phibble\Type, or one of 'numeric', 'integer', 'double', 'character', 'logical', 'factor', 'date', 'datetime', 'time' (phibble 0.6 and later), 'list'.

Returns `Selector`. Throws `InvalidArgumentException` on invalid arguments. [Example](#across-section).

### `except()` {#except}

```php
except(mixed ...$columns): Selector
```

Everything except these columns: `select(except('id'))` is dplyr's `select(-id)`.

Returns `Selector`.

### `any_selected()` {#any_selected}

```php
any_selected(mixed ...$selections): Selector
```

Columns matched by any of the selections (union).

Returns `Selector`.

### `all_selected()` {#all_selected}

```php
all_selected(mixed ...$selections): Selector
```

Columns matched by every selection (intersection, like a & b in tidyselect).

Returns `Selector`.

### `group_cols()` {#group_cols}

```php
group_cols(): Selector
```

The grouping columns.

Returns `Selector`.
