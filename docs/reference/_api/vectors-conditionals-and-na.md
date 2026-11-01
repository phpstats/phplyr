### `if_else()` {#if_else}

```php
if_else(mixed $condition, mixed $true, mixed $false, mixed $missing = null): Vec
```

Vectorised if/else. A null condition gives `$missing`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#conditionals-and-na).

### `case_when()` {#case_when}

```php
case_when(mixed ...$cases): Vec
```

The value of the first case whose condition is true, else `$default`. Cases are [condition, value] pairs: `case_when([fn ($r) => $r->x < 0, 'neg'], [fn ($r) => $r->x > 0, 'pos'], default: 'zero')`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. Throws `InvalidArgumentException` on invalid arguments.

### `case_match()` {#case_match}

```php
case_match(mixed $x, mixed ...$cases): Vec
```

Match values like a switch (deprecated in dplyr 1.2 in favor of `recode_values()`): `case_match('code', [['a', 'b'], 'vowel'], ['z', 'last'], default: 'other')`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `replace_when()` {#replace_when}

```php
replace_when(mixed $x, array ...$cases): Vec
```

Like `case_when()`, but rows matching no case keep x's value: `replace_when('score', [fn ($r) => $r->score > 8, 8])`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#conditionals-and-na).

### `recode()` {#recode}

```php
recode(mixed $_x, mixed ...$replacements): Vec
```

Replace values by name (superseded in dplyr by `recode_values()`): `recode('grade', a: 'A', b: 'B', _default: 'other')`, or with a map for keys that aren't valid names: `recode('code', [1 => 'one', 2 => 'two'])`. The first argument is dplyr's .x. Values match by their string form. Unmatched values stay as they are unless `_default` is given; NA becomes `_missing`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. An unknown underscored option throws `InvalidArgumentException`.

### `recode_values()` {#recode_values}

```php
recode_values(mixed $x, mixed ...$cases): Vec
```

Map values to new values (dplyr 1.2): `recode_values('state', ['NC', 'North Carolina'], [['NY', 'NYC'], 'New York'], default: 'other')`, or with a lookup table: `recode_values('score', from: [1, 2, 3], to: ['low', 'mid', 'high'])`. Unmatched values get default (NA by default); unmatched: 'error' throws instead.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#conditionals-and-na).

### `replace_values()` {#replace_values}

```php
replace_values(mixed $x, mixed ...$cases): Vec
```

Like `recode_values()`, but unmatched values keep their original value.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `coalesce()` {#coalesce}

```php
coalesce(mixed $x, mixed ...$values): Vec
```

First non-NA value across the arguments, row by row.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#conditionals-and-na).

### `na_if()` {#na_if}

```php
na_if(mixed $x, mixed $y): Vec
```

NA where `$x` equals `$y`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `between()` {#between}

```php
between(mixed $x, mixed $left, mixed $right): Vec
```

`$left` &lt;= `$x` &lt;= `$right`; NA when `$x` is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `near()` {#near}

```php
near(mixed $x, mixed $y, ?float $tol = null): Vec
```

Equality with tolerance, for floating point.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `when_any()` {#when_any}

```php
when_any(mixed ...$conditions): Vec
```

Elementwise `any()` across conditions (x | y | z): `when_any(fn ($r) => ..., 'flag')`. With naRm, NA inputs are ignored.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#conditionals-and-na).

### `when_all()` {#when_all}

```php
when_all(mixed ...$conditions): Vec
```

Elementwise `all()` across conditions (x & y & z).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.
