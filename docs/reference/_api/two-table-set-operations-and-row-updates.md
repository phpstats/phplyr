### `union()` {#union}

```php
union(Table $y): Closure
```

Distinct rows of both tables.

Returns a function of a table, which returns a table. Chain method: `->union()`.

### `union_all()` {#union_all}

```php
union_all(Table $y): Closure
```

All rows of both tables.

Returns a function of a table, which returns a table. Chain method: `->unionAll()`.

### `intersect()` {#intersect}

```php
intersect(Table $y): Closure
```

Distinct rows of x that also appear in y.

Returns a function of a table, which returns a table. Chain method: `->intersect()`.

### `setdiff()` {#setdiff}

```php
setdiff(Table $y): Closure
```

Distinct rows of x that don't appear in y.

Returns a function of a table, which returns a table. Chain method: `->setdiff()`. [Example](#set-operations-and-row-updates).

### `symdiff()` {#symdiff}

```php
symdiff(Table $y): Closure
```

Rows in exactly one of the tables.

Returns a function of a table, which returns a table. Chain method: `->symdiff()`.

### `setequal()` {#setequal}

```php
setequal(Table $y): Closure
```

True when both tables hold the same distinct rows, in any order.

Returns a function of a table, which returns true when the tables have the same rows. Chain method: `->setequal()`.

### `rows_insert()` {#rows_insert}

```php
rows_insert(Table $y, array|string|null $by = null, string $conflict = 'error'): Closure
```

Append rows of y whose keys are new. conflict: 'error' (default) or 'ignore' for keys that already exist. by defaults to y's first column.

Returns a function of a table, which returns a table. Chain method: `->rowsInsert()`. Throws `UnexpectedValueException` on invalid arguments.

### `rows_append()` {#rows_append}

```php
rows_append(Table $y): Closure
```

Append all rows of y.

Returns a function of a table, which returns a table. Chain method: `->rowsAppend()`.

### `rows_update()` {#rows_update}

```php
rows_update(Table $y, array|string|null $by = null, string $unmatched = 'error'): Closure
```

Overwrite x's values with y's for matching keys. unmatched: 'error' (default) or 'ignore' for y keys missing from x.

Returns a function of a table, which returns a table. Chain method: `->rowsUpdate()`.

### `rows_patch()` {#rows_patch}

```php
rows_patch(Table $y, array|string|null $by = null, string $unmatched = 'error'): Closure
```

Like `rows_update()`, but only fills x's NA values.

Returns a function of a table, which returns a table. Chain method: `->rowsPatch()`. [Example](#set-operations-and-row-updates).

### `rows_upsert()` {#rows_upsert}

```php
rows_upsert(Table $y, array|string|null $by = null): Closure
```

Update matching keys and insert new ones.

Returns a function of a table, which returns a table. Chain method: `->rowsUpsert()`. [Example](#set-operations-and-row-updates).

### `rows_delete()` {#rows_delete}

```php
rows_delete(Table $y, array|string|null $by = null, string $unmatched = 'error'): Closure
```

Delete rows of x whose keys appear in y. unmatched: 'error' or 'ignore'.

Returns a function of a table, which returns a table. Chain method: `->rowsDelete()`. Throws `UnexpectedValueException` on invalid arguments.
