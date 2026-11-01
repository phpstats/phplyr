### `group_by()` {#group_by}

```php
group_by(mixed ...$columns): Closure
```

Group by columns (names or selectors). Named arguments group by a computed column: `group_by(decade: fn ($r) => intdiv($r->year, 10) * 10)`. Options: `_add` (add to the existing groups), `_drop` (false keeps empty factor-level groups; needs backend support).

Returns a function of a table, which returns a table. Chain method: `->groupBy()`. Throws `LogicException` on invalid arguments. An unknown underscored option throws `InvalidArgumentException`. [Example](#grouping-and-ungrouping).

### `ungroup()` {#ungroup}

```php
ungroup(mixed ...$columns): Closure
```

Remove grouping, or only the named grouping columns.

Returns a function of a table, which returns a table. Chain method: `->ungroup()`. [Example](#grouping-and-ungrouping).

### `with_groups()` {#with_groups}

```php
with_groups(mixed $_groups, Closure $verb): Closure
```

Run a table function on the table grouped by `_groups`, then restore the original grouping (superseded in dplyr by `_by`): `with_groups('g', mutate(m: mean('x')))`. A null `_groups` runs it ungrouped.

Returns a function of a table, which returns a table. Chain method: `->withGroups()`.
