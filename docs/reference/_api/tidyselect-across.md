### `across()` {#across}

```php
across(mixed $cols = null, Closure|array|null $fns = null, ?string $names = null): Across
```

Apply functions to columns inside `mutate()` or `summarise()`: `summarise(across(starts_with('x'), mean(...)))`, `mutate(across(where('numeric'), fn ($v) => $v * 2))`, `summarise(across(['a', 'b'], ['avg' => mean(...), 'sd' => sd(...)]))`.

Names default to '{col}' for one function and '{col}_{fn}' for a named list.

Returns `Across`. [Example](#across-section).

### `if_any()` {#if_any}

```php
if_any(mixed $cols, Closure $predicate): Vec
```

Per-row test that a predicate holds for any of the columns, for `filter()`: `filter(if_any(starts_with('x'), fn ($v) => $v > 0))`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `if_all()` {#if_all}

```php
if_all(mixed $cols, Closure $predicate): Vec
```

Per-row test that a predicate holds for all of the columns.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `c_across()` {#c_across}

```php
c_across(mixed $cols = null): Vec
```

The values of the selected columns in the current row, for `rowwise()` tables: `rowwise()` |&gt; mutate(total: `sum(c_across(starts_with('x')))`). In a group of several rows the values run column by column.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `pick()` {#pick}

```php
pick(mixed ...$columns): Closure
```

The selected columns of the current group as a table, for use in `summarise()` closures or with `vec()`: `fn ($g) => pick('a'`, 'b')(`$g`)`->toRows()`.

Returns a function of a table, which returns a table. Chain method: `->pick()`.

### `cur_column()` {#cur_column}

```php
cur_column(): string
```

The name of the column `across()` is working on, inside one of its functions: `across(['a', 'b'], fn ($v) => cur_column().': '.$v)`.

Returns `string`. Throws `LogicException` on invalid arguments.
