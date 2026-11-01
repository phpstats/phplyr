### `pivot_longer()` {#pivot_longer}

```php
pivot_longer(mixed $cols, array|string $namesTo = 'name', string $valuesTo = 'value', ?string $namesPrefix = null, ?string $namesSep = null, ?string $namesPattern = null, bool $valuesDropNa = false, Closure|array|null $namesTransform = null, Closure|array|null $valuesTransform = null, string $colsVary = 'fastest'): Closure
```

Lengthen: turn columns into name/value rows. `pivot_longer(starts_with('wk'), namesTo: 'week', valuesTo: 'rank')`. namesTo may list several names (with namesSep or namesPattern); the name '.value' takes the value column's name from that part. namesTransform and valuesTransform are a closure, or name =&gt; closure; colsVary: 'slowest' keeps each source column's rows together.

Returns a function of a table, which returns a table. Chain method: `->pivotLonger()`. [Example](#pivoting).

### `pivot_wider()` {#pivot_wider}

```php
pivot_wider(mixed $namesFrom = 'name', mixed $valuesFrom = 'value', mixed $idCols = null, string $namesPrefix = '', string $namesSep = '_', ?string $namesGlue = null, bool $namesSort = false, mixed $valuesFill = null, Closure|array|null $valuesFn = null, string $namesVary = 'fastest', bool $namesExpand = false, bool $idExpand = false, Closure|array|null $unusedFn = null): Closure
```

Widen: spread name/value pairs into columns. `pivot_wider(namesFrom: 'week', valuesFrom: 'rank', valuesFill: 0)`. Duplicate cells need valuesFn, a closure taking the list of values. namesVary: 'slowest' groups each name's value columns together; namesExpand and idExpand add every combination of the namesFrom and idCols values (sorted, factors by level); unusedFn summarises the columns that are in none of idCols, namesFrom, and valuesFrom.

Returns a function of a table, which returns a table. Chain method: `->pivotWider()`. [Example](#pivoting).

### `build_longer_spec()` {#build_longer_spec}

```php
build_longer_spec(mixed $cols, array|string $namesTo = 'name', string $valuesTo = 'value', ?string $namesPrefix = null, ?string $namesSep = null, ?string $namesPattern = null, Closure|array|null $namesTransform = null): Closure
```

The spec `pivot_longer()` would use, as a table with .name, .value and the namesTo columns; edit it and run it with `pivot_longer_spec()`.

Returns a function of a table, which returns a table. Chain method: `->buildLongerSpec()`.

### `pivot_longer_spec()` {#pivot_longer_spec}

```php
pivot_longer_spec(Table|array $spec, bool $valuesDropNa = false, Closure|array|null $valuesTransform = null, string $colsVary = 'fastest'): Closure
```

Lengthen with a spec from `build_longer_spec()` (a table or a list of rows).

Returns a function of a table, which returns a table. Chain method: `->pivotLongerSpec()`.

### `build_wider_spec()` {#build_wider_spec}

```php
build_wider_spec(mixed $namesFrom = 'name', mixed $valuesFrom = 'value', string $namesPrefix = '', string $namesSep = '_', ?string $namesGlue = null, bool $namesSort = false, string $namesVary = 'fastest', bool $namesExpand = false): Closure
```

The spec `pivot_wider()` would use, as a table with .name, .value and the namesFrom columns; edit it and run it with `pivot_wider_spec()`.

Returns a function of a table, which returns a table. Chain method: `->buildWiderSpec()`.

### `pivot_wider_spec()` {#pivot_wider_spec}

```php
pivot_wider_spec(Table|array $spec, mixed $idCols = null, mixed $valuesFill = null): Closure
```

Widen with a spec from `build_wider_spec()`. idCols defaults to the columns the spec doesn't use.

Returns a function of a table, which returns a table. Chain method: `->pivotWiderSpec()`.

### `gather()` {#gather}

```php
gather(string $key = 'key', string $value = 'value', mixed ...$cols): Closure
```

Gather columns into key-value pairs (superseded by `pivot_longer()`): `gather('week', 'rank', 'wk1', 'wk2')`. Rows run column by column. Options: naRm, convert.

Returns a function of a table, which returns a table. Chain method: `->gather()`.

### `spread()` {#spread}

```php
spread(string $key, string $value, mixed $fill = null, bool $convert = false): Closure
```

Spread a key-value pair across columns (superseded by `pivot_wider()`): `spread('key', 'value', fill: 0)`. Columns and rows are sorted.

Returns a function of a table, which returns a table. Chain method: `->spread()`.
