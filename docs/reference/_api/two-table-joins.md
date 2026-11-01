### `inner_join()` {#inner_join}

```php
inner_join(Table $y, JoinBy|array|string|null $by = null, array $suffix = ['.x', '.y'], ?bool $keep = null, string $naMatches = 'na', string $multiple = 'all', string $unmatched = 'drop', ?string $relationship = null): Closure
```

Rows of x with a match in y, with y's columns.

Returns a function of a table, which returns a table. Chain method: `->innerJoin()`. [Example](#joins).

### `left_join()` {#left_join}

```php
left_join(Table $y, JoinBy|array|string|null $by = null, array $suffix = ['.x', '.y'], ?bool $keep = null, string $naMatches = 'na', string $multiple = 'all', string $unmatched = 'drop', ?string $relationship = null): Closure
```

All rows of x, with y's columns where they match (NA otherwise).

Returns a function of a table, which returns a table. Chain method: `->leftJoin()`. [Example](#joins).

### `right_join()` {#right_join}

```php
right_join(Table $y, JoinBy|array|string|null $by = null, array $suffix = ['.x', '.y'], ?bool $keep = null, string $naMatches = 'na', string $multiple = 'all', string $unmatched = 'drop', ?string $relationship = null): Closure
```

All rows of y, with x's columns where they match.

Returns a function of a table, which returns a table. Chain method: `->rightJoin()`.

### `full_join()` {#full_join}

```php
full_join(Table $y, JoinBy|array|string|null $by = null, array $suffix = ['.x', '.y'], ?bool $keep = null, string $naMatches = 'na', string $multiple = 'all', ?string $relationship = null): Closure
```

All rows of both tables.

Returns a function of a table, which returns a table. Chain method: `->fullJoin()`. [Join two tables](../examples/join.qmd).

### `semi_join()` {#semi_join}

```php
semi_join(Table $y, JoinBy|array|string|null $by = null, string $naMatches = 'na'): Closure
```

Rows of x that have a match in y (x's columns only).

Returns a function of a table, which returns a table. Chain method: `->semiJoin()`.

### `anti_join()` {#anti_join}

```php
anti_join(Table $y, JoinBy|array|string|null $by = null, string $naMatches = 'na'): Closure
```

Rows of x that have no match in y.

Returns a function of a table, which returns a table. Chain method: `->antiJoin()`. [Example](#joins).

### `cross_join()` {#cross_join}

```php
cross_join(Table $y, array $suffix = ['.x', '.y']): Closure
```

Every combination of rows of x and y.

Returns a function of a table, which returns a table. Chain method: `->crossJoin()`.

### `nest_join()` {#nest_join}

```php
nest_join(Table $y, JoinBy|array|string|null $by = null, ?bool $keep = null, string $name = 'data', string $naMatches = 'na'): Closure
```

All rows of x, with a list-column (name) holding each row's matching rows of y as a table. y's equality keys are left out unless keep: true.

Returns a function of a table, which returns a table. Chain method: `->nestJoin()`.
