### `str_detect()` {#str_detect}

```php
str_detect(mixed $x, mixed $pattern, bool $negate = false): Vec
```

Does the pattern occur?

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_starts()` {#str_starts}

```php
str_starts(mixed $x, mixed $pattern, bool $negate = false): Vec
```

Does the string start with the pattern?

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_ends()` {#str_ends}

```php
str_ends(mixed $x, mixed $pattern, bool $negate = false): Vec
```

Does the string end with the pattern?

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_count()` {#str_count}

```php
str_count(mixed $x, mixed $pattern): Vec
```

Number of matches.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_extract()` {#str_extract}

```php
str_extract(mixed $x, mixed $pattern): Vec
```

The first match, or NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_extract_all()` {#str_extract_all}

```php
str_extract_all(mixed $x, mixed $pattern): Vec
```

Every match, as a list per row (a list-column).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_replace()` {#str_replace}

```php
str_replace(mixed $x, mixed $pattern, mixed $replacement): Vec
```

Replace the first match; \1 refers to a group.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_replace_all()` {#str_replace_all}

```php
str_replace_all(mixed $x, mixed $pattern, mixed $replacement): Vec
```

Replace every match; \1 refers to a group.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_split()` {#str_split}

```php
str_split(mixed $x, mixed $pattern): Vec
```

Split into pieces, as a list per row (a list-column).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_split_i()` {#str_split_i}

```php
str_split_i(mixed $x, mixed $pattern, int $i): Vec
```

The i-th piece of a split (1-based; negative counts from the end).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_pad()` {#str_pad}

```php
str_pad(mixed $x, int $width, string $side = 'left', string $pad = ' '): Vec
```

Pad to a width: side 'left' (default), 'right', or 'both'.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_trunc()` {#str_trunc}

```php
str_trunc(mixed $x, int $width, string $side = 'right', string $ellipsis = '...'): Vec
```

Truncate to a width, marking the cut with an ellipsis.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_trim()` {#str_trim}

```php
str_trim(mixed $x, string $side = 'both'): Vec
```

Trim whitespace: side 'both', 'left', or 'right'.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_squish()` {#str_squish}

```php
str_squish(mixed $x): Vec
```

Trim and collapse internal whitespace.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_sub()` {#str_sub}

```php
str_sub(mixed $x, int $start = 1, ?int $end = null): Vec
```

Substring by 1-based positions, as stringr's `str_sub()`; negative counts from the end.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_length()` {#str_length}

```php
str_length(mixed $x): Vec
```

Number of characters.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_to_title()` {#str_to_title}

```php
str_to_title(mixed $x): Vec
```

Title case: the first letter of each word uppercase.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_to_upper()` {#str_to_upper}

```php
str_to_upper(mixed $x): Vec
```

Uppercase, as stringr's `str_to_upper()`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_to_lower()` {#str_to_lower}

```php
str_to_lower(mixed $x): Vec
```

Lowercase, as stringr's `str_to_lower()`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_upper()` {#str_upper}

```php
str_upper(mixed $x): Vec
```

Uppercase.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `str_lower()` {#str_lower}

```php
str_lower(mixed $x): Vec
```

Lowercase.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `paste()` {#paste}

```php
paste(mixed ...$xs): Vec
```

Paste values together, separated by sep (default a space); NA becomes "NA", as in R.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `paste0()` {#paste0}

```php
paste0(mixed ...$xs): Vec
```

Paste values together; NA becomes "NA", as in R. Strings after the first are literals.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `fixed()` {#fixed}

```php
fixed(string $pattern): Vec
```

A literal pattern: `str_detect('name', fixed('a.b'))`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `regex()` {#regex}

```php
regex(string $pattern, bool $ignoreCase = false): Vec
```

A regular expression with options: `regex('^ab', ignoreCase: true)`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.
