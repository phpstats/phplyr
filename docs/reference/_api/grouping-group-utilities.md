### `group_vars()` {#group_vars}

```php
group_vars(): Closure
```

The grouping column names.

Returns a function of a table, which returns the grouping column names. Chain method: `->groupVars()`. [Example](#grouping-and-ungrouping).

### `n_groups()` {#n_groups}

```php
n_groups(): Closure
```

The number of groups (1 for an ungrouped table).

Returns a function of a table, which returns the number of groups. Chain method: `->nGroups()`. [Example](#grouping-and-ungrouping).

### `group_size()` {#group_size}

```php
group_size(): Closure
```

Rows in each group, in group order.

Returns a function of a table, which returns the number of rows in each group. Chain method: `->groupSize()`. [Example](#grouping-and-ungrouping).

### `group_indices()` {#group_indices}

```php
group_indices(): Closure
```

Each row's 1-based group number.

Returns a function of a table, which returns each row's group number, from 1. Chain method: `->groupIndices()`.

### `group_keys()` {#group_keys}

```php
group_keys(): Closure
```

One row per group with the key columns.

Returns a function of a table, which returns a table. Chain method: `->groupKeys()`.

### `group_rows()` {#group_rows}

```php
group_rows(): Closure
```

Each group's 1-based row numbers, in group order.

Returns a function of a table, which returns the 1-based row numbers of each group. Chain method: `->groupRows()`.

### `group_data()` {#group_data}

```php
group_data(): Closure
```

`group_keys()` with a .rows list-column of each group's 1-based row numbers.

Returns a function of a table, which returns a table. Chain method: `->groupData()`.

### `group_trim()` {#group_trim}

```php
group_trim(): Closure
```

Drop unused factor levels from the grouping columns, and with them the empty groups that drop: false kept.

Returns a function of a table, which returns a table. Chain method: `->groupTrim()`.

### `group_split()` {#group_split}

```php
group_split(mixed ...$options): Closure
```

Split into a list of ungrouped tables, one per group. With `_keep:` false the grouping columns are dropped. Option `_by` groups first.

Returns a function of a table, which returns one table per group. Chain method: `->groupSplit()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#grouping-and-ungrouping).

### `group_map()` {#group_map}

```php
group_map(Closure $fn, bool $_keep = false): Closure
```

Call `fn (Table $group, array $key)` for each group and return the list of results. With `_keep:` true the group table keeps the grouping columns.

Returns a function of a table, which returns one result per group. Chain method: `->groupMap()`.

### `group_modify()` {#group_modify}

```php
group_modify(Closure $fn): Closure
```

Replace each group with the table `fn (Table $group, array $key)` returns, then put the key columns back in front. The result stays grouped.

Returns a function of a table, which returns a table. Chain method: `->groupModify()`. Throws `UnexpectedValueException` on invalid arguments.

### `group_walk()` {#group_walk}

```php
group_walk(Closure $fn): Closure
```

Call `fn (Table $group, array $key)` for each group; return the table unchanged.

Returns a function of a table, which returns the table unchanged, after calling the function on each group. Chain method: `->groupWalk()`.

### `group_nest()` {#group_nest}

```php
group_nest(mixed ...$options): Closure
```

One row per group: the key columns and a list-column of each group's other columns as a table. Options: `_key` (the list-column, 'data'), `_keep` (also keep the key columns in the nested tables).

Returns a function of a table, which returns a table. Chain method: `->groupNest()`. An unknown underscored option throws `InvalidArgumentException`.

### `nest_by()` {#nest_by}

```php
nest_by(mixed ...$cols): Closure
```

Nest by the given columns into a rowwise table: one row per group, with a list-column of tables. Options: key ('data'), keep (keep the grouping columns in the nested tables).

Returns a function of a table, which returns a table. Chain method: `->nestBy()`. An unknown underscored option throws `InvalidArgumentException`.

### `cur_group_id()` {#cur_group_id}

```php
cur_group_id(): CurGroup
```

The current group's 1-based number, inside `mutate()`, `summarise()`, or `reframe()`.

Returns `CurGroup`.

### `cur_group_rows()` {#cur_group_rows}

```php
cur_group_rows(): CurGroup
```

The current group's 1-based row numbers in the table. In `mutate()` each row gets its own row number; in `reframe()` each group gets the list.

Returns `CurGroup`.

### `cur_group()` {#cur_group}

```php
cur_group(): CurGroup
```

The current group's key, as an array of grouping column =&gt; value (dplyr returns a one-row tibble). In `mutate()` each row gets its group's key.

Returns `CurGroup`.
