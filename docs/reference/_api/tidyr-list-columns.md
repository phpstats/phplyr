### `nest()` {#nest}

```php
nest(mixed ...$spec): Closure
```

Nest columns into a list-column of tables: `nest(data: ['x', 'y'])`, or `nest()` on a grouped table to nest everything but the groups.

Returns a function of a table, which returns a table. Chain method: `->nest()`. Throws `InvalidArgumentException` on invalid arguments.

### `unnest()` {#unnest}

```php
unnest(array|string $cols, bool $keepEmpty = false): Closure
```

Unnest list-columns of tables back into rows.

Returns a function of a table, which returns a table. Chain method: `->unnest()`.

### `unnest_longer()` {#unnest_longer}

```php
unnest_longer(mixed $col, ?string $valuesTo = null, ?string $indicesTo = null, ?bool $indicesInclude = null, bool $keepEmpty = false): Closure
```

Turn each element of list-columns into rows: `unnest_longer('tags')`. Elements with names add an index column of the names ({col}`_id`); with indicesInclude: true, unnamed elements add 1-based positions. Empty and NA elements drop their row unless keepEmpty: true. valuesTo and indicesTo may use {col}. Several columns must have elements of the same length in each row, or length one.

Returns a function of a table, which returns a table. Chain method: `->unnestLonger()`. [Example](#list-columns).

### `unnest_wider()` {#unnest_wider}

```php
unnest_wider(mixed $col, ?string $namesSep = null): Closure
```

Turn each element of list-columns into columns: `unnest_wider('meta')`. Named elements give a column per name, in order of first appearance; unnamed elements need namesSep, which names them {col}{sep}1, {col}{sep}2... (namesSep also prefixes named ones). Missing names and NA elements become NA.

Returns a function of a table, which returns a table. Chain method: `->unnestWider()`. [Example](#list-columns).

### `hoist()` {#hoist}

```php
hoist(string $col, mixed ...$paths): Closure
```

Pull components out of a list-column into new columns, before it: `hoist('meta', name: 'name', first: 1, city: ['address', 'city'])`. A path is a name, a 1-based position, or a list of them for nested elements. With `_remove` (the default), hoisted components are removed from the list-column, and the column is dropped once every element is empty.

Returns a function of a table, which returns a table. Chain method: `->hoist()`. An unknown underscored option throws `InvalidArgumentException`. [Example](#list-columns).

### `chop()` {#chop}

```php
chop(mixed $cols): Closure
```

Collapse rows into list-columns: `chop(['x', 'y'])` gives one row per combination of the other columns (in order of first appearance), with x and y as lists.

Returns a function of a table, which returns a table. Chain method: `->chop()`. [Example](#list-columns).

### `unchop()` {#unchop}

```php
unchop(mixed $cols, bool $keepEmpty = false): Closure
```

Expand list-columns into rows, the reverse of `chop()`. Empty and NA elements drop their row unless keepEmpty: true.

Returns a function of a table, which returns a table. Chain method: `->unchop()`.

### `pack()` {#pack}

```php
pack(mixed ...$spec): Closure
```

Pack columns into a column of named arrays (tidyr's data frame columns): `pack(dims: ['width', 'height'])`. Packed columns go at the end. With `_namesSep`, the inner names drop their outer name and the separator.

Returns a function of a table, which returns a table. Chain method: `->pack()`. An unknown underscored option throws `InvalidArgumentException`.

### `unpack()` {#unpack}

```php
unpack(mixed $cols, ?string $namesSep = null): Closure
```

Spread columns of named arrays into columns, the reverse of `pack()`.

Returns a function of a table, which returns a table. Chain method: `->unpack()`.
