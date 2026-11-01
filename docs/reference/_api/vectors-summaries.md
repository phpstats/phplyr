### `n()` {#n}

```php
n(): Vec
```

Number of rows in the group.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Summarize groups](../examples/group-and-summarize.qmd).

### `n_distinct()` {#n_distinct}

```php
n_distinct(mixed ...$columns): Vec
```

Number of distinct values (or combinations); NA counts unless naRm.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `sum()` {#sum}

```php
sum(mixed $x, bool $naRm = false): Vec
```

Sum of x; NA unless naRm is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Summarize groups](../examples/group-and-summarize.qmd).

### `mean()` {#mean}

```php
mean(mixed $x, bool $naRm = false): Vec
```

Arithmetic mean of x; NA unless naRm is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. [Summarize groups](../examples/group-and-summarize.qmd).

### `median()` {#median}

```php
median(mixed $x, bool $naRm = false): Vec
```

Median of x; NA unless naRm is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `sd()` {#sd}

```php
sd(mixed $x, bool $naRm = false): Vec
```

Sample standard deviation (n - 1).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `var_()` {#var_}

```php
var_(mixed $x, bool $naRm = false): Vec
```

Sample variance (n - 1): R's `var()`. Named `var_()` because var is reserved in PHP, as in phibble and phpolars.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `variance()` {#variance}

```php
variance(mixed $x, bool $naRm = false): Vec
```

Alias of `var_()`.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `quantile()` {#quantile}

```php
quantile(mixed $x, float $p, bool $naRm = false): Vec
```

Type 7 quantile, R's default.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers. Throws `InvalidArgumentException` on invalid arguments.

### `iqr()` {#iqr}

```php
iqr(mixed $x, bool $naRm = false): Vec
```

Interquartile range (type 7).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `min()` {#min}

```php
min(mixed $x, bool $naRm = false): Vec
```

Smallest value of x; NA unless naRm is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `max()` {#max}

```php
max(mixed $x, bool $naRm = false): Vec
```

Largest value of x; NA unless naRm is true.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `first()` {#first}

```php
first(mixed $x, mixed $default = null, mixed $orderBy = null, bool $naRm = false): Vec
```

First value; `$default` when the group is empty.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `last()` {#last}

```php
last(mixed $x, mixed $default = null, mixed $orderBy = null, bool $naRm = false): Vec
```

Last value; `$default` when the group is empty.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `nth()` {#nth}

```php
nth(mixed $x, int $n, mixed $default = null, mixed $orderBy = null, bool $naRm = false): Vec
```

The `$n`-th value, 1-based; negative counts from the end.

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `any()` {#any}

```php
any(mixed $x, bool $naRm = false): Vec
```

True when any value is true (R's `any()`; NA when undecided).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.

### `all()` {#all}

```php
all(mixed $x, bool $naRm = false): Vec
```

True when all values are true (R's `all()`; NA when undecided).

Returns a vector helper (`Vec`). Its expression runs on a database table when its arguments are columns, literals, or other helpers.
