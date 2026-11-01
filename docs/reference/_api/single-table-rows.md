### `filter()` {#filter}

```php
filter(mixed ...$conditions): Closure
```

Keep rows where every condition is true; NA counts as false. Conditions are row closures `fn ($r, $group)`, vector helpers such as `between('x', 1, 5)` or `if_any(...)`, or a logical column name. Option: `_by`.

Returns a function of a table, which returns a table. Chain method: `->filter()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#rows).

### `filter_out()` {#filter_out}

```php
filter_out(mixed ...$conditions): Closure
```

Drop rows where every condition is true (dplyr 1.2). NA counts as false, so rows with NA conditions are kept: `filter()` and `filter_out()` together see every row exactly once. Option: `_by`.

Returns a function of a table, which returns a table. Chain method: `->filterOut()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#rows).

### `arrange()` {#arrange}

```php
arrange(mixed ...$keys): Closure
```

Sort rows by columns or expressions; `desc()` reverses. Stable; NA last. Ignores grouping unless `_byGroup:` true.

Returns a function of a table, which returns a table. Chain method: `->arrange()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#rows).

### `desc()` {#desc}

```php
desc(mixed $x): Desc
```

Descending sort key for `arrange()`, and for rank helpers: `min_rank(desc('x'))`.

Returns `Desc`. [Example](#rows).

### `distinct()` {#distinct}

```php
distinct(mixed ...$columns): Closure
```

Unique rows over the given columns (all columns when none are given). Named arguments add computed columns first. Option: `_keepAll` (keep every column, from the first row of each combination).

Returns a function of a table, which returns a table. Chain method: `->distinct()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#columns).

### `slice()` {#slice}

```php
slice(array|int ...$positions): Closure
```

Rows by 1-based position within each group, like dplyr's `slice()`: `slice(1, 2)`, `slice(range(1, 5))`, or negative positions to drop rows. Option: `_by`.

Returns a function of a table, which returns a table. Chain method: `->slice()`. An unknown underscored option throws `InvalidArgumentException`.

### `slice_head()` {#slice_head}

```php
slice_head(?int $n = null, ?float $prop = null, mixed $_by = null): Closure
```

First rows of each group: `slice_head(n: 3)` or `slice_head(prop: 0.1)`.

Returns a function of a table, which returns a table. Chain method: `->sliceHead()`.

### `slice_tail()` {#slice_tail}

```php
slice_tail(?int $n = null, ?float $prop = null, mixed $_by = null): Closure
```

Last rows of each group.

Returns a function of a table, which returns a table. Chain method: `->sliceTail()`.

### `slice_min()` {#slice_min}

```php
slice_min(mixed $orderBy, ?int $n = null, ?float $prop = null, bool $withTies = true, bool $naRm = false, mixed $_by = null): Closure
```

Rows with the smallest values of `$orderBy` in each group, ordered by it.

Returns a function of a table, which returns a table. Chain method: `->sliceMin()`.

### `slice_max()` {#slice_max}

```php
slice_max(mixed $orderBy, ?int $n = null, ?float $prop = null, bool $withTies = true, bool $naRm = false, mixed $_by = null): Closure
```

Rows with the largest values of `$orderBy` in each group, ordered by it.

Returns a function of a table, which returns a table. Chain method: `->sliceMax()`. [Example](#rows).

### `slice_sample()` {#slice_sample}

```php
slice_sample(?int $n = null, ?float $prop = null, bool $replace = false, mixed $weightBy = null, mixed $_by = null): Closure
```

Random rows from each group. Uses `mt_rand()`, so `mt_srand()` makes it reproducible. `$weightBy` is a column of non-negative weights.

Returns a function of a table, which returns a table. Chain method: `->sliceSample()`.

### `head()` {#head}

```php
head(int $n = 6): Closure
```

First `$n` rows of the whole table, ignoring groups (like utils::head).

Returns a function of a table, which returns a table. Chain method: `->head()`. [Example](#rows).

### `top_n()` {#top_n}

```php
top_n(int $n, mixed $wt = null): Closure
```

The n rows with the largest wt in each group, ties included, in their original order (superseded by `slice_max()`). A negative n takes the smallest. Without wt, the last column.

Returns a function of a table, which returns a table. Chain method: `->topN()`.

### `top_frac()` {#top_frac}

```php
top_frac(float $n, mixed $wt = null): Closure
```

`top_n()` with a proportion of each group's rows (superseded by `slice_max(prop:)`).

Returns a function of a table, which returns a table. Chain method: `->topFrac()`.

### `sample_n()` {#sample_n}

```php
sample_n(int $size, bool $replace = false, mixed $weight = null): Closure
```

n random rows from each group (superseded by `slice_sample()`).

Returns a function of a table, which returns a table. Chain method: `->sampleN()`.

### `sample_frac()` {#sample_frac}

```php
sample_frac(float $size = 1.0, bool $replace = false, mixed $weight = null): Closure
```

A proportion of random rows from each group (superseded by `slice_sample(prop:)`).

Returns a function of a table, which returns a table. Chain method: `->sampleFrac()`.

### `add_row()` {#add_row}

```php
add_row(mixed ...$values): Closure
```

Add rows of values: `add_row(x: 4, y: 'd')`, or several with lists: `add_row(x: [4, 5])`. Columns not given are NA. Options: `_before`, `_after` (a 1-based row; the default adds at the end).

Returns a function of a table, which returns a table. Chain method: `->addRow()`. Throws `InvalidArgumentException` on invalid arguments. An unknown underscored option throws `InvalidArgumentException`.
