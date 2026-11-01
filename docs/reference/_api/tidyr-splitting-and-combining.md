### `separate_wider_delim()` {#separate_wider_delim}

```php
separate_wider_delim(mixed $cols, string $delim, ?array $names = null, ?string $namesSep = null, string $tooFew = 'error', string $tooMany = 'error', bool $colsRemove = true): Closure
```

Split columns on a fixed delimiter into new columns: `separate_wider_delim('name', ' ', names: ['first', 'last'])`. Without names, give namesSep to name them col{sep}1, col{sep}2... tooFew: 'error'|'align_start'|'align_end'; tooMany: 'error'|'drop'|'merge'.

Returns a function of a table, which returns a table. Chain method: `->separateWiderDelim()`. Throws `InvalidArgumentException`, `UnexpectedValueException` on invalid arguments. [Example](#splitting-and-combining).

### `separate_wider_position()` {#separate_wider_position}

```php
separate_wider_position(mixed $cols, array $widths, string $tooFew = 'error', bool $colsRemove = true): Closure
```

Split columns by character widths: `separate_wider_position('code', ['state' => 2, 1, 'county' => 3])`; unnamed widths are skipped.

Returns a function of a table, which returns a table. Chain method: `->separateWiderPosition()`. Throws `UnexpectedValueException` on invalid arguments.

### `separate_wider_regex()` {#separate_wider_regex}

```php
separate_wider_regex(mixed $cols, array $patterns, string $tooFew = 'error', bool $colsRemove = true): Closure
```

Split columns with regular expressions: `separate_wider_regex('id', ['letters' => '[a-z]+', '-', 'digits' => '\d+'])`. Unnamed patterns match but are not kept. Rows that don't match error unless tooFew: 'align_start'.

Returns a function of a table, which returns a table. Chain method: `->separateWiderRegex()`. Throws `UnexpectedValueException` on invalid arguments.

### `separate_longer_delim()` {#separate_longer_delim}

```php
separate_longer_delim(mixed $cols, string $delim): Closure
```

Split columns on a fixed delimiter into rows: `separate_longer_delim('tags', ',')`. Several columns must split into the same number of pieces (or one).

Returns a function of a table, which returns a table. Chain method: `->separateLongerDelim()`. [Example](#splitting-and-combining).

### `separate_longer_position()` {#separate_longer_position}

```php
separate_longer_position(mixed $cols, int $width): Closure
```

Split columns into rows of `$width` characters.

Returns a function of a table, which returns a table. Chain method: `->separateLongerPosition()`.

### `separate()` {#separate}

```php
separate(string $col, array $into, array|string|int $sep = '[^[:alnum:]]+', bool $remove = true, bool $convert = false, string $extra = 'warn', string $fill = 'warn'): Closure
```

Split a string column into several (superseded in tidyr, still common): `separate('date', ['year', 'month'], sep: '-')`. sep is a regular expression, or an int / list of ints for positions. extra: 'warn'|'drop'|'merge'; fill: 'warn'|'right'|'left'; convert: R's type.`convert()`.

Returns a function of a table, which returns a table. Chain method: `->separate()`.

### `separate_rows()` {#separate_rows}

```php
separate_rows(mixed ...$cols): Closure
```

Split columns on a regular expression into rows (superseded by `separate_longer_delim()`): `separate_rows('tags', sep: ',')`. Options: sep (default '[^[:alnum:].]+'), convert.

Returns a function of a table, which returns a table. Chain method: `->separateRows()`.

### `extract()` {#extract}

```php
extract(string $col, array $into, string $regex = '([[:alnum:]]+)', bool $remove = true, bool $convert = false): Closure
```

Pull regular-expression groups into new columns (superseded by `separate_wider_regex()`): `extract('x', ['letter', 'digit'], '([a-z])(\d)')`. A null name skips a group. Rows that don't match get NA.

Returns a function of a table, which returns a table. Chain method: `->extract()`.

### `unite()` {#unite}

```php
unite(string $col, mixed ...$cols): Closure
```

Paste columns together into one: `unite('date', 'year', 'month', sep: '-')`. Options: sep ('_'), remove (true), naRm (false; NA otherwise prints "NA").

Returns a function of a table, which returns a table. Chain method: `->unite()`. [Example](#splitting-and-combining).
