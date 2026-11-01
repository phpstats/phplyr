### `col()` {#col}

```php
col(string $name): Vec
```

A column as a vector: `mutate(y: col('x'))`, `col('price')->mul(1.1)`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#database-tables).

### `lit()` {#lit}

```php
lit(mixed $value): Vec
```

A literal value as a vector.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `vec()` {#vec}

```php
vec(Closure $fn): Vec
```

A row closure as a vector, for use where a Vec is expected.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `is_na()` {#is_na}

```php
is_na(mixed $x): Vec
```

True where x is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `abs()` {#abs}

```php
abs(mixed $x): Vec
```

Absolute value.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `sqrt()` {#sqrt}

```php
sqrt(mixed $x): Vec
```

Square root.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `exp()` {#exp}

```php
exp(mixed $x): Vec
```

Exponential, e to the power x.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `log()` {#log}

```php
log(mixed $x, ?float $base = null): Vec
```

Natural logarithm, or base `$base`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `log10()` {#log10}

```php
log10(mixed $x): Vec
```

Base-10 logarithm.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `round()` {#round}

```php
round(mixed $x, int $digits = 0): Vec
```

R's `round()`: halves to even, at `$digits` decimal places.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `floor()` {#floor}

```php
floor(mixed $x): Vec
```

Largest integer not greater than x.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `ceiling()` {#ceiling}

```php
ceiling(mixed $x): Vec
```

Smallest integer not less than x.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `as_int()` {#as_int}

```php
as_int(mixed $x): Vec
```

Convert to integer.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `as_float()` {#as_float}

```php
as_float(mixed $x): Vec
```

Convert to double.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `as_string()` {#as_string}

```php
as_string(mixed $x): Vec
```

Convert to string.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `as_bool()` {#as_bool}

```php
as_bool(mixed $x): Vec
```

Convert to boolean.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->add()` {#method-add}

```php
$vec->add(mixed $y): Vec
```

x + y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->sub()` {#method-sub}

```php
$vec->sub(mixed $y): Vec
```

x - y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->mul()` {#method-mul}

```php
$vec->mul(mixed $y): Vec
```

x \* y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->div()` {#method-div}

```php
$vec->div(mixed $y): Vec
```

x / y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->mod()` {#method-mod}

```php
$vec->mod(mixed $y): Vec
```

x modulo y, R's %%: the result takes the sign of y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->pow()` {#method-pow}

```php
$vec->pow(mixed $y): Vec
```

x to the power y.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->neg()` {#method-neg}

```php
$vec->neg(): Vec
```

Negation, -x.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->eq()` {#method-eq}

```php
$vec->eq(mixed $y): Vec
```

x == y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->ne()` {#method-ne}

```php
$vec->ne(mixed $y): Vec
```

x != y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->lt()` {#method-lt}

```php
$vec->lt(mixed $y): Vec
```

x &lt; y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->le()` {#method-le}

```php
$vec->le(mixed $y): Vec
```

x &lt;= y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->gt()` {#method-gt}

```php
$vec->gt(mixed $y): Vec
```

x &gt; y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->ge()` {#method-ge}

```php
$vec->ge(mixed $y): Vec
```

x &gt;= y; NA when either side is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Example](#database-tables).

### `->between()` {#method-between}

```php
$vec->between(mixed $low, mixed $high): Vec
```

low &lt;= x &lt;= high, inclusive.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->isIn()` {#method-isin}

```php
$vec->isIn(array $values): Vec
```

True where x is one of the values, R's %in%; never NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->isNa()` {#method-isna}

```php
$vec->isNa(): Vec
```

True where x is NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->and()` {#method-and}

```php
$vec->and(mixed $y): Vec
```

Logical and, with R's NA rules: false and NA is false.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->or()` {#method-or}

```php
$vec->or(mixed $y): Vec
```

Logical or, with R's NA rules: true or NA is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->not()` {#method-not}

```php
$vec->not(): Vec
```

Logical not; NA stays NA.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `->desc()` {#method-desc}

```php
$vec->desc(): Desc
```

Sort in descending order, for `arrange()` and order_by.

Returns `Desc`.
